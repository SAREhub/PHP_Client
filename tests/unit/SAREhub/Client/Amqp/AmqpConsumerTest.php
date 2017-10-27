<?php

namespace unit\SAREhub\Client\Amqp;

use Hamcrest\Matchers;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpConsumer;
use SAREhub\Client\Amqp\AmqpConsumerOptions;
use SAREhub\Client\Amqp\AmqpMessageProcessConfirmStrategy;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Processor\Processor;

class AmqpConsumerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Mock | AmqpMessageProcessConfirmStrategy
     */
    private $processConfirmStrategy;

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
        $this->processConfirmStrategy = \Mockery::mock(AmqpMessageProcessConfirmStrategy::class);
        $this->processor = \Mockery::mock(Processor::class);
        $this->consumer = new AmqpConsumer(AmqpConsumerOptions::newInstance()
            ->setProcessor($this->processor)
            ->setProcessConfirmStrategy($this->processConfirmStrategy)
        );
    }

    public function testProcess()
    {
        $exchange = BasicExchange::withIn(BasicMessage::newInstance());
        $this->processor->expects("process")->with($exchange);
        $this->processConfirmStrategy->expects("confirm")->with(
            Matchers::allOf(Matchers::equalTo($exchange), Matchers::not(Matchers::sameInstance($exchange))),
            $exchange
        );
        $this->consumer->process($exchange);
    }
}
