<?php

namespace SAREhub\Client\Amqp;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpMessageHeaders as AMH;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Message\Message;


class BasicAmqpProcessConfirmStrategyTest extends TestCase
{

    use MockeryPHPUnitIntegration;
    /**
     * @var Mock | AmqpChannelWrapper
     */
    private $channel;

    /**
     * @var BasicAmqpProcessConfirmStrategy
     */
    private $strategy;

    /**
     * @var Message
     */
    private $orginalIn;


    private $consumer;

    /**
     * @var AmqpConsumerOptions
     */
    private $consumerOptions;

    protected function setUp()
    {
        $this->channel = \Mockery::mock(AmqpChannelWrapper::class);
        $this->strategy = new BasicAmqpProcessConfirmStrategy();
        $this->orginalIn = BasicMessage::newInstance()
            ->setHeader(AMH::CONSUMER_TAG, "test_tag")
            ->setHeader(AMH::DELIVERY_TAG, "1");

        $this->consumer = \Mockery::mock(AmqpConsumer::class);
        $this->consumerOptions = new AmqpConsumerOptions();
        $this->consumer->shouldReceive("getOptions")->andReturn($this->consumerOptions);
        $this->channel->shouldReceive("getConsumer")->with("test_tag")->andReturn($this->consumer);
    }

    public function testConfirmWhenSuccessAndAutoAckMode()
    {
        $this->consumerOptions->setAutoAckMode();
        $this->channel->shouldNotReceive("ack");
        $this->strategy->confirm($this->channel, $this->orginalIn, $this->createSuccessedExchange());
    }

    public function testConfirmWhenMessageSuccessProcessedAndAckMode()
    {
        $this->channel->expects("ack")->with($this->orginalIn);
        $this->strategy->confirm($this->channel, $this->orginalIn, $this->createSuccessedExchange());
    }

    public function testConfirmWhenMessageFailedProcessedAndAckMode()
    {
        $this->channel->expects("reject")->with($this->orginalIn, true);
        $this->strategy->confirm($this->channel, $this->orginalIn, $this->createFailedExchange());
    }

    public function testConfirmWhenMessageFailedProcessedAndRequeueFalseAndAckMode()
    {
        $this->strategy->setRejectRequeue(false);
        $this->channel->expects("reject")->with($this->orginalIn, false);
        $this->strategy->confirm($this->channel, $this->orginalIn, $this->createFailedExchange());
    }

    private function createSuccessedExchange(): Exchange
    {
        return BasicExchange::newInstance();
    }

    private function createFailedExchange(): Exchange
    {
        return BasicExchange::newInstance()->setException(new \Exception());
    }
}
