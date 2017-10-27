<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Message\Message;
use SAREhub\Commons\Service\ServiceSupport;

class AmqpChannelWrapper extends ServiceSupport
{
    const DEFAULT_WAIT_TIMEOUT = 2;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var AmqpMessageConverter
     */
    private $messageConverter;

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
    }

    public function registerConsumer(AmqpConsumer $consumer)
    {
        $opts = $consumer->getOptions();
        $this->consumers[$opts->getTag()] = $consumer;

        $this->getRealChannel()->basic_consume(
            $opts->getQueueName(),
            $opts->getTag(),
            false,
            $opts->isAutoAck(),
            $opts->isExclusive(),
            $this->getOnMessageCallback(),
            null,
            $opts->getConsumeArguments()
        );
    }

    public function getOnMessageCallback(): array
    {
        return [$this, "onMessage"];
    }

    public function unregisterConsumer(string $consumerTag)
    {
        $consumer = $this->getConsumer($consumerTag);
        $this->getRealChannel()->basic_cancel($consumer->getOptions()->getTag(), false, true);
        unset($this->consumers[$consumerTag]);
    }

    public function wait()
    {
        try {
            $this->getRealChannel()->wait(null, true, $this->getWaitTimeout());
        } catch (AMQPTimeoutException $e) {
            $this->getLogger()->debug("channel wait timeout: " . $e->getMessage(), ["exception" => $e]);
        }
    }

    public function onMessage(AMQPMessage $in)
    {
        $exchange = $this->createExchange($in);
        $this->getLogger()->debug('got message', ['message' => $exchange->getIn()]);
        $consumer = $this->getConsumer($exchange->getIn()->getHeader(AmqpMessageHeaders::CONSUMER_TAG));
        $consumer->process($exchange);
    }

    private function createExchange(AMQPMessage $in)
    {
        $converted = $this->messageConverter->convertFrom($in);
        return BasicExchange::withIn($converted);
    }

    private function confirmProcess(Exchange $exchange, $deliveryTag)
    {
        if ($exchange->isFailed()) {
            $this->getLogger()->debug('process failed ', ['exchange' => $exchange]);
            $this->nack($deliveryTag);
        } else {
            $this->getLogger()->debug('process success ', ['exchange' => $exchange]);
            $this->ack($deliveryTag);
        }
    }

    public function ack(string $deliveryTag, bool $multiple = false)
    {
        $this->getRealChannel()->basic_ack($deliveryTag, $multiple);
    }

    public function nack(string $deliveryTag, bool $multiple = false, $requeue = false)
    {
        $this->getRealChannel()->basic_nack($deliveryTag, $multiple, $requeue);
    }

    public function reject(string $deliveryTag, bool $requeue = false)
    {
        $this->getRealChannel()->basic_reject($deliveryTag, $requeue);
    }

    public function publish(Message $message)
    {
        $converted = $this->messageConverter->convertTo($message);
        $exchange = $message->getHeader(AmqpMessageHeaders::EXCHANGE);
        $routingKey = $message->getHeader(AmqpMessageHeaders::ROUTING_KEY);
        $this->getRealChannel()->basic_publish($converted, $exchange, $routingKey);
    }

    public function setChannelPrefetchCount(int $count, int $size = 0)
    {
        $this->getRealChannel()->basic_qos($size, $count, true);
    }

    public function setPrefetchCountPerConsumer(int $count, int $size = 0)
    {
        $this->getRealChannel()->basic_qos($size, $count, false);
    }

    public function getRealChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function getWaitTimeout(): int
    {
        return $this->waitTimeout;
    }

    public function setWaitTimeout(int $timeout)
    {
        $this->waitTimeout = $timeout;
    }

    public function hasConsumer(string $consumerTag)
    {
        return isset($this->consumers[$consumerTag]);
    }

    public function getConsumer(string $consumerTag): AmqpConsumer
    {
        if ($this->hasConsumer($consumerTag)) {
            return $this->consumers[$consumerTag];
        }

        throw new \InvalidArgumentException(sprintf("consumer with tag: '%s' is not registered", $consumerTag));
    }

    public function getConsumers(): array
    {
        return $this->consumers;
    }

    public function getMessageConverter(): AmqpMessageConverter
    {
        return $this->messageConverter;
    }

    public function setMessageConverter(AmqpMessageConverter $converter)
    {
        $this->messageConverter = $converter;
    }
}