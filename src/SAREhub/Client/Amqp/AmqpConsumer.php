<?php

namespace SAREhub\Client\Amqp;

use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\Processor;

class AmqpConsumer implements Processor
{
    /**
     * @var AmqpConsumerOptions
     */
    private $options;

    /**
     * @var Processor
     */
    private $processor;

    public function __construct(AmqpConsumerOptions $options, Processor $processor)
    {
        $this->options = $options;
        $this->processor = $processor;
    }

    public function process(Exchange $exchange)
    {
        $this->processor->process($exchange);
    }

    public function getOptions(): AmqpConsumerOptions
    {
        return $this->options;
    }

    public function getProcessor(): Processor
    {
        return $this->processor;
    }
}