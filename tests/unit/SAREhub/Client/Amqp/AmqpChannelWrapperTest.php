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
     * @var Mockery\Mock | AmqpProcessConfirmStrategy
     */
    private $processConfirmStrategy;

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
        $this->wrapper = new AmqpChannelWrapper($this->channel);

        $this->messageConverter = Mockery::mock(AmqpMessageConverter::class);
        $this->wrapper->setMessageConverter($this->messageConverter);

        $this->processConfirmStrategy = Mockery::mock(AmqpProcessConfirmStrategy::class);
        $this->wrapper->setProcessConfirmStrategy($this->processConfirmStrategy);

        $this->consumer = $this->createConsumer(AmqpConsumerOptions::newInstance()
            ->setQueueName("test_queue")
            ->setTag("test_consumer_tag")
            ->setExclusive(true));

        //$this->channel->allows(["basic_consume" => $this->consumer->getOptions()->getTag()]);
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
            $opts->isAutoAckMode(),
            $opts->isExclusive(),
            false,
            [$this->wrapper, "onMessage"],
            null,
            Matchers::equalTo($opts->getConsumeArguments())
        )->andReturn($opts->getTag());

        $this->wrapper->registerConsumer($this->consumer);
        $this->assertSame($this->consumer, $this->wrapper->getConsumer($opts->getTag()));
    }

    public function testUnregisterConsumerThenChannelBasicCancel()
    {
        $opts = $this->consumer->getOptions();
        $this->channel->allows(["basic_consume" => $opts->getTag()]);
        $this->wrapper->registerConsumer($this->consumer);

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

    public function testTickThenChannelWait()
    {
        $this->wrapper->start();
        $this->channel->expects("wait")->with(null, true, $this->wrapper->getWaitTimeout());
        $this->wrapper->tick();
    }

    public function testTickWhenTimeoutExceptionThenLog()
    {
        $this->wrapper->start();
        $e = new AMQPTimeoutException("exception message");

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects("debug")->with("channel wait timeout: exception message", ["exception" => $e]);
        $this->wrapper->setLogger($logger);

        $this->channel->expects("wait")->andThrow($e);
        $this->wrapper->tick();
    }

    public function testOnMessage()
    {
        $this->channel->allows(["basic_consume" => $this->consumer->getOptions()->getTag()]);
        $this->wrapper->registerConsumer($this->consumer);

        $inMessage = new AMQPMessage();
        $converted = BasicMessage::newInstance()->setHeader(AMH::CONSUMER_TAG, "test_consumer_tag");
        $this->messageConverter->expects("convertFrom")->with($inMessage)->andReturn($converted);

        $expectedExchange = BasicExchange::withIn($converted);
        $this->consumer->expects("process")->with(Matchers::equalTo($expectedExchange));

        $this->processConfirmStrategy->expects("confirm")->with(
            $this->wrapper,
            $converted,
            Matchers::equalTo($expectedExchange)
        );

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
        $message = BasicMessage::newInstance()->setHeader(AMH::DELIVERY_TAG, "1");
        $this->channel->expects("basic_ack")->with("1", false);
        $this->wrapper->ack($message);
    }

    public function testRejectWhenNotRequeue()
    {
        $message = BasicMessage::newInstance()->setHeader(AMH::DELIVERY_TAG, "1");
        $this->channel->expects("basic_reject")->with("1", false);
        $this->wrapper->reject($message, false);
    }

    public function testRejectWhenRequeue()
    {
        $message = BasicMessage::newInstance()->setHeader(AMH::DELIVERY_TAG, "1");
        $this->channel->expects("basic_reject")->with("1", true);
        $this->wrapper->reject($message, true);
    }

    private function createConsumer(AmqpConsumerOptions $options): AmqpConsumer
    {
        return Mockery::mock(AmqpConsumer::class, ["getOptions" => $options]);
    }
}
