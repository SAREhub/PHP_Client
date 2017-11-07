<?php

namespace SAREhub\Client\Amqp;


use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\Processor;

class AmqpProducer implements Processor
{
    /**
     * @var AmqpChannelWrapper
     */
    private $channel;

    public function __construct(AmqpChannelWrapper $channel)
    {
        $this->channel = $channel;
    }

    public function process(Exchange $exchange)
    {
        $this->getChannel()->publish($exchange->getIn());
    }

    public function getChannel(): AmqpChannelWrapper
    {
        return $this->channel;
    }


}