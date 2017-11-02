<?php

namespace SAREhub\Client\Amqp;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpMessageHeaders as AMH;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\TransformProcessor;

class AmqpChannelWrapperIT extends TestCase
{
    /**
     * @var AmqpChannelWrapper
     */
    private $channel;

    private $queueName = "AmqpChannelWrapperIT";

    protected function setUp()
    {
        $connection = AmqpTestHelper::createConnection();
        $this->channel = new AmqpChannelWrapper($connection->channel());
        $this->channel->getWrappedChannel()->queue_declare($this->queueName);
    }

    public function testPublishAndConsumeFromQueue()
    {
        $publishedMessage = BasicMessage::newInstance()
            ->setBody("test_" . mt_rand(1, 100000))
            ->setHeader(AMH::ROUTING_KEY, $this->queueName);

        $this->channel->publish($publishedMessage);

        $consumedMessage = BasicMessage::newInstance();
        $this->channel->registerConsumer(new AmqpConsumer(AmqpConsumerOptions::newInstance(),
            TransformProcessor::transform(function (Exchange $exchange) use (&$consumedMessage) {
                $consumedMessage = $exchange->getIn();
            })));

        $this->channel->start();
        $this->channel->tick();

        $this->assertEquals($publishedMessage->getBody(), $consumedMessage->getBody(), "body");
    }
}
