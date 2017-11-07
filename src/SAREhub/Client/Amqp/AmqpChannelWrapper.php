<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\Message;
use SAREhub\Commons\Service\ServiceSupport;

class AmqpChannelWrapper extends ServiceSupport
{
    const DEFAULT_WAIT_TIMEOUT = 1;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var AmqpMessageConverter
     */
    private $messageConverter;

    /**
     * @var AmqpProcessConfirmStrategy
     */
    private $processConfirmStrategy;

    /**
     * @var int
     */
    private $waitTimeout = self::DEFAULT_WAIT_TIMEOUT;

    /**
     * @var AmqpConsumer[]
     */
    private $consumers = [];

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
        $this->messageConverter = new AmqpMessageConverter();
        $this->processConfirmStrategy = new BasicAmqpProcessConfirmStrategy();
    }

    public function registerConsumer(AmqpConsumer $consumer)
    {
        $opts = $consumer->getOptions();
        $this->getLogger()->debug("registering consumer", ["options" => $opts]);
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
        $opts->setTag($tag);

        $this->getLogger()->debug("consumer tag: $tag", ["options" => $opts]);
        $this->consumers[$tag] = $consumer;
    }

    public function unregisterConsumer(string $consumerTag)
    {
        $consumer = $this->getConsumer($consumerTag);
        $this->getWrappedChannel()->basic_cancel($consumer->getOptions()->getTag(), false, true);
        unset($this->consumers[$consumerTag]);
    }

    public function onMessage(AMQPMessage $in)
    {
        $inConverted = $this->getMessageConverter()->convertFrom($in);
        $this->getLogger()->debug('onMessage', ['message' => $inConverted]);

        $consumerTag = $inConverted->getHeader(AmqpMessageHeaders::CONSUMER_TAG);
        $exchange = BasicExchange::withIn($inConverted);
        $consumer = $this->getConsumer($consumerTag);
        $consumer->process($exchange);
        $this->getProcessConfirmStrategy()->confirm($this, $inConverted, $exchange);
    }

    protected function doTick()
    {
        try {
            $this->getWrappedChannel()->wait(null, true, $this->getWaitTimeout());
        } catch (AMQPTimeoutException $e) {
            $this->getLogger()->debug("channel wait timeout: " . $e->getMessage(), ["exception" => $e]);
        }
    }

    public function ack(Message $message)
    {
        $this->getLogger()->debug("ack", ["message" => $message]);
        $deliveryTag = $message->getHeader(AmqpMessageHeaders::DELIVERY_TAG);
        $this->getWrappedChannel()->basic_ack($deliveryTag, false);
    }

    public function reject(Message $message, bool $requeue = true)
    {
        $this->getLogger()->debug("reject", ["message" => $message, "requeue" => $requeue]);
        $deliveryTag = $message->getHeader(AmqpMessageHeaders::DELIVERY_TAG);
        $this->getWrappedChannel()->basic_reject($deliveryTag, $requeue);
    }

    public function publish(Message $message)
    {
        $this->getLogger()->debug("publish", ["message" => $message]);

        $converted = $this->messageConverter->convertTo($message);
        $exchange = $message->getHeader(AmqpMessageHeaders::EXCHANGE);
        $routingKey = $message->getHeader(AmqpMessageHeaders::ROUTING_KEY);
        $this->getWrappedChannel()->basic_publish($converted, $exchange, $routingKey);
    }

    public function setChannelPrefetchCount(int $count, int $size = 0)
    {
        $this->getWrappedChannel()->basic_qos($size, $count, true);
    }

    public function setPrefetchCountPerConsumer(int $count, int $size = 0)
    {
        $this->getWrappedChannel()->basic_qos($size, $count, false);
    }

    public function getWaitTimeout(): int
    {
        return $this->waitTimeout;
    }

    public function setWaitTimeout(int $timeout)
    {
        $this->waitTimeout = $timeout;
    }

    public function getConsumer(string $consumerTag): AmqpConsumer
    {
        if ($this->hasConsumer($consumerTag)) {
            return $this->consumers[$consumerTag];
        }

        throw new \InvalidArgumentException(sprintf("consumer with tag: '%s' is not registered", $consumerTag));
    }

    public function hasConsumer(string $consumerTag)
    {
        return isset($this->consumers[$consumerTag]);
    }

    public function getConsumers(): array
    {
        return $this->consumers;
    }

    public function getWrappedChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function getMessageConverter(): AmqpMessageConverter
    {
        return $this->messageConverter;
    }

    public function setMessageConverter(AmqpMessageConverter $messageConverter)
    {
        $this->messageConverter = $messageConverter;
    }

    public function getProcessConfirmStrategy(): AmqpProcessConfirmStrategy
    {
        return $this->processConfirmStrategy;
    }

    public function setProcessConfirmStrategy(AmqpProcessConfirmStrategy $processConfirmStrategy)
    {
        $this->processConfirmStrategy = $processConfirmStrategy;
    }
}