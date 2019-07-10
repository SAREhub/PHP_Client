<?php

namespace SAREhub\Client\Amqp;

use Hamcrest\Matchers;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpMessageHeaders as AMH;
use SAREhub\Client\Amqp\Schema\AmqpEnvironmentSchemaCreator;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class AmqpChannelWrapperTest extends TestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * @var MockInterface | AMQPChannel
     */
    private $channel;

    /**
     * @var MockInterface | AmqpMessageConverter
     */
    private $messageConverter;

    /**
     * @var MockInterface | AmqpProcessConfirmStrategy
     */
    private $processConfirmStrategy;

    /**
     * @var AmqpChannelWrapper
     */
    private $wrapper;

    /**
     * @var MockInterface | AmqpConsumer
     */
    private $consumer;

    /**
     * @var MockInterface | AmqpEnvironmentSchemaCreator
     */
    private $schemaCreator;

    protected function setUp()
    {
        $this->channel = Mockery::mock(AMQPChannel::class);
        $this->schemaCreator = Mockery::mock(AmqpEnvironmentSchemaCreator::class);
        $this->wrapper = new AmqpChannelWrapper($this->schemaCreator);
        $this->wrapper->setWrappedChannel($this->channel);

        $this->messageConverter = Mockery::mock(AmqpMessageConverter::class);
        $this->wrapper->setMessageConverter($this->messageConverter);

        $this->processConfirmStrategy = Mockery::mock(AmqpProcessConfirmStrategy::class);
        $this->wrapper->setProcessConfirmStrategy($this->processConfirmStrategy);

        $this->consumer = $this->createConsumer(AmqpConsumerOptions::newInstance()
            ->setQueueName("test_queue")
            ->setTag("test_consumer_tag")
            ->setExclusive(true));
        $this->consumer->allows("getTag")->andReturn($this->consumer->getOptions()->getTag());
    }

    public function testSetChannelPrefetch()
    {
        $this->schemaCreator->allows("create");

        $count = 1;
        $size = 2;
        $this->channel->expects("basic_qos")->with($size, $count, true);

        $this->wrapper->setChannelPrefetch($count, $size);
        $this->wrapper->updateState();
    }

    public function testSetConsumerPrefetch()
    {
        $this->schemaCreator->allows("create");

        $count = 1;
        $size = 2;
        $this->channel->expects("basic_qos")->with($size, $count, false);

        $this->wrapper->setConsumerPrefetch($count, $size);
        $this->wrapper->updateState();
    }

    public function testRegisterConsumerThenChannelBasicConsumeWhenLazy()
    {
        $this->channel->expects("basic_consume")->never();
        $this->wrapper->registerConsumer($this->consumer, true);

        $this->assertSame($this->consumer, $this->wrapper->getConsumer($this->consumer->getTag()));
    }

    public function testRegisterConsumerThenChannelBasicConsumeWhenNotLazy()
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
        $this->consumer->expects("setTag")->with($opts->getTag());

        $this->wrapper->registerConsumer($this->consumer, false);

        $this->assertSame($this->consumer, $this->wrapper->getConsumer($opts->getTag()));
    }

    public function testUnregisterConsumerThenChannelBasicCancel()
    {
        $consumerTag = $this->consumer->getOptions()->getTag();
        $this->channel->allows(["basic_consume" => $consumerTag]);
        $this->wrapper->registerConsumer($this->consumer);

        $this->channel->expects("basic_cancel")->with($consumerTag, false, true);

        $this->wrapper->unregisterConsumer($consumerTag);

        $this->assertFalse($this->wrapper->hasConsumer($consumerTag));
    }

    public function testCancelConsumeWhenConsumerNotRegistered()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("consumer with tag: 'test_consumer_tag' is not registered");
        $this->wrapper->unregisterConsumer($this->consumer->getOptions()->getTag());
    }

    public function testTickThenChannelWait()
    {
        $this->schemaCreator->expects("create")->with($this->channel);
        $this->wrapper->start();
        $this->channel->expects("wait")->with(null, true, $this->wrapper->getWaitTimeout());
        $this->wrapper->tick();
    }

    public function testTickWhenTimeoutExceptionThenSilent()
    {
        $this->schemaCreator->expects("create")->with($this->channel);
        $this->wrapper->start();
        $e = new AMQPTimeoutException("exception message");

        $this->channel->expects("wait")->andThrow($e);
        $this->wrapper->tick();
        $this->assertTrue(true); // silent
    }

    public function testTickWhenInterruptedSystemCallThenSilent()
    {
        $this->schemaCreator->expects("create")->with($this->channel);
        $this->wrapper->start();

        $e =  new \ErrorException("stream_select ... Interrupted system call");
        $this->channel->expects("wait")->andThrow($e);

        $this->wrapper->tick();
    }

    public function testTickWhenOtherExceptionThenThrow()
    {
        $this->schemaCreator->expects("create")->with($this->channel);
        $this->wrapper->start();

        $e =  new \Exception("other");
        $this->channel->expects("wait")->andThrow($e);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("other");

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
