<?php

namespace SAREhub\Client\Amqp;

use Hamcrest\Matchers;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SAREhub\Client\Amqp\AmqpMessageHeaders as AMH;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class AmqpChannelWrapperTest extends TestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * @var Mockery\Mock | AMQPChannel
     */
    private $channel;

    /**
     * @var Mockery\Mock | AmqpMessageConverter
     */
    private $messageConverter;

    /**
     * @var AmqpChannelWrapper
     */
    private $wrapper;

    /**
     * @var Mockery\Mock | AmqpConsumer
     */
    private $consumer;

    protected function setUp()
    {
        $this->channel = Mockery::mock(AMQPChannel::class);
        $this->messageConverter = Mockery::mock(AmqpMessageConverter::class);
        $this->wrapper = new AmqpChannelWrapper($this->channel);
        $this->wrapper->setMessageConverter($this->messageConverter);

        $this->consumer = Mockery::mock(AmqpConsumer::class, [
            "getOptions" => AmqpConsumerOptions::newInstance()
                ->setQueueName("test_queue")
                ->setTag("test_consumer_tag")
                ->setExclusive(true)
        ]);
    }

    public function testSetChannelPrefetchCount()
    {
        $count = 1;
        $size = 2;
        $this->channel->expects("basic_qos")->with($size, $count, true);
        $this->wrapper->setChannelPrefetchCount($count, $size);
    }

    public function testSetPrefetchCountPerConsumer()
    {
        $count = 1;
        $size = 2;
        $this->channel->expects("basic_qos")->with($size, $count, false);
        $this->wrapper->setPrefetchCountPerConsumer($count, $size);
    }

    public function testRegisterConsumerThenChannelBasicConsume()
    {
        $opts = $this->consumer->getOptions();
        $this->channel->expects("basic_consume")->with(
            $opts->getQueueName(),
            $opts->getTag(),
            false,
            $opts->isAutoAck(),
            $opts->isExclusive(),
            [$this->wrapper, "onMessage"],
            null,
            Matchers::equalTo($opts->getConsumeArguments())
        );

        $this->wrapper->registerConsumer($this->consumer);
        $this->assertSame($this->consumer, $this->wrapper->getConsumer($opts->getTag()));
    }

    public function testUnregisterConsumerThenChannelBasicCancel()
    {
        $this->channel->allows("basic_consume");
        $this->wrapper->registerConsumer($this->consumer);

        $opts = $this->consumer->getOptions();
        $this->channel->expects("basic_cancel")->with($opts->getTag(), false, true);
        $this->wrapper->unregisterConsumer($this->consumer->getOptions()->getTag());

        $this->assertFalse($this->wrapper->hasConsumer($this->consumer->getOptions()->getTag()));
    }

    public function testCancelConsumeWhenConsumerNotRegistered()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("consumer with tag: 'test_consumer_tag' is not registered");
        $this->wrapper->unregisterConsumer($this->consumer->getOptions()->getTag());
    }

    public function testWaitThenChannelWait()
    {
        $this->channel->expects("wait")->with(null, true, $this->wrapper->getWaitTimeout());
        $this->wrapper->wait();
    }

    public function testWaitWhenConsumerAndTimeoutExceptionThenLog()
    {
        $e = new AMQPTimeoutException("test");

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects("debug")->with(
            "channel wait timeout: " . $e->getMessage(),
            ["exception" => $e]
        );
        $this->wrapper->setLogger($logger);

        $this->channel->expects("wait")->andThrow($e);
        $this->wrapper->wait();
    }

    public function testOnMessageThenConvertMessage()
    {
        $this->channel->allows("basic_consume");
        $this->consumer->allows("process");

        $this->wrapper->registerConsumer($this->consumer);
        $inMessage = new AMQPMessage("test");
        $converted = BasicMessage::newInstance()
            ->setHeader(AMH::CONSUMER_TAG, $this->consumer->getOptions()->getTag());

        $this->messageConverter->expects("convertFrom")->with($inMessage)->andReturn($converted);
        $this->wrapper->onMessage($inMessage);
    }

    public function testOnMessageThenConsumerProcess()
    {
        $this->channel->allows("basic_consume");
        $this->wrapper->registerConsumer($this->consumer);

        $inMessage = new AMQPMessage("test");
        $converted = BasicMessage::newInstance()->setHeader(AMH::CONSUMER_TAG, $this->consumer->getOptions()->getTag());
        $this->messageConverter->allows("convertFrom")->andReturn($converted);
        $this->consumer->expects("process")->with(Matchers::equalTo(BasicExchange::withIn($converted)));
        $this->wrapper->onMessage($inMessage);
    }

    public function testPublishThenChannelBasicPublish()
    {
        $exchange = "test_exchange";
        $routingKey = 'part1.part2';

        $message = BasicMessage::newInstance()->setHeaders([
            AMH::EXCHANGE => $exchange,
            AMH::ROUTING_KEY => $routingKey
        ]);

        $convertedMessage = new AMQPMessage();
        $this->messageConverter->expects("convertTo")->with($message)->andReturn($convertedMessage);
        $this->channel->expects("basic_publish")->with($convertedMessage, $exchange, $routingKey);
        $this->wrapper->publish($message);
    }

    public function testAck()
    {
        $this->channel->expects("basic_ack")->with("1", false);
        $this->wrapper->ack("1", false);
    }

    public function testNack()
    {
        $this->channel->expects("basic_nack")->with("1", false, true);
        $this->wrapper->nack("1", false, true);
    }

    public function testReject()
    {
        $this->channel->expects("basic_reject")->with("1", true);
        $this->wrapper->reject("1", true);
    }
}
