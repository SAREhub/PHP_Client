<?php

namespace SAREhub\Client\Amqp;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class AmqpProducerTest extends TestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * @var Mock
     */
    private $channel;

    /**
     * @var AmqpProducer
     */
    private $producer;

    protected function setUp()
    {
        $this->channel = \Mockery::mock(AmqpChannelWrapper::class);
        $this->producer = new AmqpProducer($this->channel);
    }

    public function testProcessThenChannelPublish()
    {
        $message = BasicMessage::newInstance();
        $this->channel->expects("publish")->with($message);
        $this->producer->process(BasicExchange::withIn($message));
    }
}
