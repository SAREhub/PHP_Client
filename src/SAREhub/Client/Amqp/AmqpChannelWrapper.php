<?php

namespace SAREhub\Client\Amqp;

use ErrorException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Amqp\Schema\AmqpEnvironmentSchemaCreator;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\Message;
use SAREhub\Commons\Service\ServiceSupport;

class AmqpChannelWrapper extends ServiceSupport
{
    /**
     * @var AmqpChannelWrapperState
     */
    private $state;

    /**
     * @var AmqpMessageConverter
     */
    private $messageConverter;

    /**
     * @var AmqpProcessConfirmStrategy
     */
    private $processConfirmStrategy;

    /**
     * @var AmqpEnvironmentSchemaCreator
     */
    private $schemaCreator;

    public function __construct(AmqpEnvironmentSchemaCreator $schemaCreator)
    {
        $this->state = new AmqpChannelWrapperState();
        $this->messageConverter = new AmqpMessageConverter();
        $this->processConfirmStrategy = new BasicAmqpProcessConfirmStrategy();
        $this->schemaCreator = $schemaCreator;
    }

    public function registerConsumer(AmqpConsumer $consumer, bool $lazy = true): void
    {
        $this->getLogger()->info("Registering consumer", ["options" => $consumer->getOptions()]);

        $this->state->addConsumer($consumer);
        if ($lazy) {
            return;
        }

        $opts = $consumer->getOptions();
        $tag = $this->getWrappedChannel()->basic_consume(
            $opts->getQueueName(),
            $opts->getTag(),
            false,
            $opts->isAutoAckMode(),
            $opts->isExclusive(),
            false,
            [$this, "onMessage"],
            null,
            $opts->getConsumeArguments()
        );
        $consumer->setTag($tag);
        $this->getLogger()->notice("Registered consumer with tag: $tag", ["options" => $opts]);
    }

    public function onMessage(AMQPMessage $in): void
    {
        $inConverted = $this->getMessageConverter()->convertFrom($in);
        $this->getLogger()->info("Got message", ["message" => $inConverted]);
        $consumerTag = $inConverted->getHeader(AmqpMessageHeaders::CONSUMER_TAG);
        $exchange = BasicExchange::withIn($inConverted->copy());
        $this->getConsumer($consumerTag)->process($exchange);
        $this->getProcessConfirmStrategy()->confirm($this, $inConverted, $exchange);
    }

    public function unregisterConsumer(string $consumerTag): void
    {
        $consumer = $this->getConsumer($consumerTag);
        $this->getWrappedChannel()->basic_cancel($consumerTag, false, true);
        $this->state->removeConsumer($consumer);
        $this->getLogger()->notice("Unregistered consumer with tag: $consumerTag", ["options" => $consumer->getOptions()]);
    }

    protected function doStart()
    {
        $this->updateState();
    }

    protected function doTick()
    {
        try {
            $this->getWrappedChannel()->wait(null, true, $this->state->getWaitTimeout());
        } catch (AMQPTimeoutException $e) {
            // ignore wait timeout
        } catch (ErrorException $e) {
            if (strpos($e->getMessage(), "Interrupted system call") === false) { // silent for signal process
                throw $e;
            }
        }
    }

    public function ack(Message $message)
    {
        $deliveryTag = $message->getHeader(AmqpMessageHeaders::DELIVERY_TAG);
        $this->getWrappedChannel()->basic_ack($deliveryTag, false);
        $this->getLogger()->info("Ack message", ["message" => $message]);
    }

    public function reject(Message $message, bool $requeue = true)
    {
        $deliveryTag = $message->getHeader(AmqpMessageHeaders::DELIVERY_TAG);
        $this->getWrappedChannel()->basic_reject($deliveryTag, $requeue);
        $this->getLogger()->info("Rejected message", ["message" => $message, "requeue" => $requeue]);
    }

    public function publish(Message $message)
    {
        $converted = $this->messageConverter->convertTo($message);
        $exchange = $message->getHeader(AmqpMessageHeaders::EXCHANGE);
        $routingKey = $message->getHeader(AmqpMessageHeaders::ROUTING_KEY);
        $this->getWrappedChannel()->basic_publish($converted, $exchange, $routingKey);
        $this->getLogger()->info("Published message", ["message" => $message]);
    }

    public function updateState(): void
    {
        $this->getLogger()->info("Updating state");
        $this->schemaCreator->create($this->getWrappedChannel());
        $this->updatePrefetchState();
        $this->updateConsumersState();
        $this->getLogger()->notice("Updated state");
    }

    private function updatePrefetchState(): void
    {
        if ($this->state->getChannelPrefetchCount() > 0 || $this->state->getChannelPrefetchSize() > 0) {
            $this->getWrappedChannel()->basic_qos(
                $this->state->getChannelPrefetchSize(),
                $this->state->getChannelPrefetchCount(),
                true
            );
        }

        if ($this->state->getConsumerPrefetchCount() > 0 || $this->state->getConsumerPrefetchSize() > 0) {
            $this->getWrappedChannel()->basic_qos(
                $this->state->getConsumerPrefetchSize(),
                $this->state->getConsumerPrefetchCount(),
                false
            );
        }
        $this->getLogger()->notice("Updated prefetch state");
    }

    private function updateConsumersState(): void
    {
        $consumers = $this->state->getConsumers();
        $this->state->setConsumers([]); // clean before re-register
        foreach ($consumers as $consumer) {
            $this->registerConsumer($consumer, false);
        }
        $this->getLogger()->notice("Updated consumers state");
    }

    public function setChannelPrefetch(int $count, int $size = 0): void
    {
        $this->state->setChannelPrefetch($count, $size);
    }

    public function setConsumerPrefetch(int $count, int $size = 0): void
    {
        $this->state->setConsumerPrefetch($count, $size);
    }

    public function getWaitTimeout(): int
    {
        return $this->state->getWaitTimeout();
    }

    public function setWaitTimeout(int $timeout): void
    {
        $this->state->setWaitTimeout($timeout);
    }

    public function getConsumer(string $consumerTag): AmqpConsumer
    {
        return $this->state->getConsumer($consumerTag);
    }

    public function hasConsumer(string $consumerTag): bool
    {
        return $this->state->hasConsumer($consumerTag);
    }

    public function getWrappedChannel(): AMQPChannel
    {
        return $this->state->getChannel();
    }

    public function setWrappedChannel(AMQPChannel $channel): void
    {
        $this->state->setChannel($channel);
    }

    public function getMessageConverter(): AmqpMessageConverter
    {
        return $this->messageConverter;
    }

    public function setMessageConverter(AmqpMessageConverter $messageConverter): void
    {
        $this->messageConverter = $messageConverter;
    }

    public function getProcessConfirmStrategy(): AmqpProcessConfirmStrategy
    {
        return $this->processConfirmStrategy;
    }

    public function setProcessConfirmStrategy(AmqpProcessConfirmStrategy $processConfirmStrategy): void
    {
        $this->processConfirmStrategy = $processConfirmStrategy;
    }
}
