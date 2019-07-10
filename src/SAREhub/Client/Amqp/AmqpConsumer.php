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

    /**
     * @var string
     */
    private $tag;

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

    public function getTag(): string
    {
        return empty($this->getOptions()->getTag()) ? $this->tag : $this->getOptions()->getTag();
    }

    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }
}