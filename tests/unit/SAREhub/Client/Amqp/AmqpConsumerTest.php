<?php

namespace unit\SAREhub\Client\Amqp;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpConsumer;
use SAREhub\Client\Amqp\AmqpConsumerOptions;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Processor\Processor;

class AmqpConsumerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Mock | Processor
     */
    private $processor;

    /**
     * @var AmqpConsumer
     */
    private $consumer;

    protected function setUp()
    {
        $this->processor = \Mockery::mock(Processor::class);
        $this->consumer = new AmqpConsumer(AmqpConsumerOptions::newInstance(), $this->processor);
    }

    public function testProcess()
    {
        $exchange = BasicExchange::withIn(BasicMessage::newInstance());
        $this->processor->expects("process")->with($exchange);
        $this->consumer->process($exchange);
    }
}
