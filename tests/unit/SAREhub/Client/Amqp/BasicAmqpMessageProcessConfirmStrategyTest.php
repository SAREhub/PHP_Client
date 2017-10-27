<?php

namespace SAREhub\Client\Amqp;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpMessageHeaders as AMH;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;


class BasicAmqpMessageProcessConfirmStrategyTest extends TestCase
{

    use MockeryPHPUnitIntegration;
    /**
     * @var Mock | AmqpChannelWrapper
     */
    private $channel;

    /**
     * @var BasicAmqpMessageProcessConfirmStrategy
     */
    private $strategy;

    protected function setUp()
    {
        $this->channel = \Mockery::mock(AmqpChannelWrapper::class);
        $this->strategy = new BasicAmqpMessageProcessConfirmStrategy($this->channel);
    }

    public function testConfirmWhenMessageSuccessProcessed()
    {
        $orginal = BasicExchange::withIn(BasicMessage::newInstance()
            ->setHeader(AMH::DELIVERY_TAG, "1")
        );

        $afterProcess = BasicExchange::newInstance();

        $this->channel->expects("ack")->with("1", false);
        $this->strategy->confirm($orginal, $afterProcess);
    }

    public function testConfirmWhenMessageFailedProcessed()
    {
        $orginal = BasicExchange::withIn(BasicMessage::newInstance()
            ->setHeader(AMH::DELIVERY_TAG, "1")
        );

        $afterProcess = BasicExchange::newInstance()->setException(new \Exception());

        $this->channel->expects("nack")->with("1", false, true);
        $this->strategy->confirm($orginal, $afterProcess);
    }

    public function testConfirmWhenMessageFailedProcessedAndRequeueFalse()
    {
        $orginal = BasicExchange::withIn(BasicMessage::newInstance()
            ->setHeader(AMH::DELIVERY_TAG, "1")
        );

        $afterProcess = BasicExchange::newInstance()->setException(new \Exception());

        $this->channel->expects("nack")->with("1", false, false);
        $this->strategy->setRequeueFailed(false);

        $this->strategy->confirm($orginal, $afterProcess);
    }


}
