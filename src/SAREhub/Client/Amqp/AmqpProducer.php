<?php

namespace SAREhub\Client\Amqp;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\Processor;

class AmqpProducer implements Processor, LoggerAwareInterface
{

    /**
     * @var AmqpChannelWrapper
     */
    private $channel;

    /**
     * @var AmqpMessageConverter
     */
    private $converter;

    private $logger;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * @return AmqpProducer
     */
    public static function newInstance()
    {
        return new self();
    }

    public function withChannel(AmqpChannelWrapper $channel)
    {
        $this->channel = $channel;
        return $this;
    }

    public function withConverter(AmqpMessageConverter $converter)
    {
        $this->converter = $converter;
        return $this;
    }

    public function process(Exchange $exchange)
    {
        $in = $exchange->getIn();
        $this->getLogger()->debug('publishing message', ['message' => $in]);

        $message = $this->converter->convertTo($in);
        $publishExchange = $in->getHeader(AmqpMessageHeaders::EXCHANGE);
        $routingKey = (string)$in->getHeader(AmqpMessageHeaders::ROUTING_KEY);
        $this->getChannel()->publish($message, $publishExchange, $routingKey);
    }

    /**
     * @return AmqpChannelWrapper
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __toString()
    {
        return 'AmqpProducer';
    }
}