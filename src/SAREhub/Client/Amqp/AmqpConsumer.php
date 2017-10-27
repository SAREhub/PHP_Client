<?php

namespace SAREhub\Client\Amqp;

use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\Processor;

class AmqpConsumer implements Processor
{
    /**
     * @var AmqpConsumerOptions
     */
    private $options;

    public function __construct(AmqpConsumerOptions $options)
    {
        $this->options = $options;
    }

    public function process(Exchange $exchange)
    {
        $orginal = BasicExchange::newInstance()->setIn($exchange->getIn()->copy());

        $this->getOptions()->getProcessor()->process($exchange);
        $this->getOptions()->getProcessConfirmStrategy()->confirm($orginal, $exchange);
    }

    public function getOptions(): AmqpConsumerOptions
    {
        return $this->options;
    }
}