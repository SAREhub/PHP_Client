<?php


namespace SAREhub\Client\Amqp;


use SAREhub\Client\Processor\Processor;
use SAREhub\Commons\Misc\InvokableProvider;

class MessageConsumerProvider extends InvokableProvider
{
    /**
     * @var AmqpConsumerOptions
     */
    private $consumerOptions;

    /**
     * @var Processor
     */
    private $processor;

    public function __construct(AmqpConsumerOptions $consumerOptions, Processor $processor)
    {
        $this->consumerOptions = $consumerOptions;
        $this->processor = $processor;
    }

    public function get()
    {
        return new AmqpConsumer($this->consumerOptions, $this->processor);
    }
}