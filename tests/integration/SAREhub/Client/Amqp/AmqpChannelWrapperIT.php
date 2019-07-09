<?php

namespace SAREhub\Client\Amqp;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpMessageHeaders as AMH;
use SAREhub\Client\Amqp\Schema\AmqpEnvironmentManager;
use SAREhub\Client\Amqp\Schema\AmqpEnvironmentSchema;
use SAREhub\Client\Amqp\Schema\AmqpEnvironmentSchemaCreator;
use SAREhub\Client\Amqp\Schema\AmqpQueueSchema;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Message;
use SAREhub\Client\Processor\ExchangeCatcherProcessor;

class AmqpChannelWrapperIT extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var AmqpChannelWrapper
     */
    private $channel;

    /**
     * @var AmqpProducer
     */
    private $producer;

    /**
     * @var AmqpConnectionService
     */
    private $connectionService;

    private $queueName = "AmqpChannelWrapperIT";

    /**
     * @var ExchangeCatcherProcessor
     */
    private $consumerProcessor;

    protected function setUp()
    {
        $this->channel = $this->createChannel();
        $this->connectionService = new AmqpConnectionService(AmqpTestHelper::createConnectionProvider());
        $this->connectionService->addChannel($this->channel);
        $this->producer = new AmqpProducer($this->channel);
        $this->connectionService->start();
    }

    private function createChannel(): AmqpChannelWrapper
    {
        $schema = AmqpEnvironmentSchema::newInstance()
            ->addQueueSchema(AmqpQueueSchema::newInstance()->withName($this->queueName)->withAutoDelete(true));
        $schemaCreator = new AmqpEnvironmentSchemaCreator(new AmqpEnvironmentManager(), $schema);
        $channel = new AmqpChannelWrapper($schemaCreator);
        $this->consumerProcessor = new ExchangeCatcherProcessor();
        $opts = AmqpConsumerOptions::newInstance()->setQueueName($this->queueName);
        $channel->registerConsumer(new AmqpConsumer($opts, $this->consumerProcessor));
        return $channel;
    }

    protected function tearDown()
    {
        $this->connectionService->stop();
    }

    public function testPublishAndConsumeFromQueue()
    {
        $publishedMessage = BasicMessage::newInstance()
            ->setBody("test_" . mt_rand(1, 100000))
            ->setHeader(AMH::ROUTING_KEY, $this->queueName);
        $this->publishAndWaitForMessage($publishedMessage);

        $messages = $this->consumerProcessor->getCaughtInMessages();
        $this->assertCount(1, $messages);
        $message = $messages[0];

        $this->assertEquals($publishedMessage->getBody(), $message->getBody(), "message.body");
    }

    private function publishAndWaitForMessage(Message $message, int $timeout = 5)
    {
        $exchange = BasicExchange::withIn($message);
        $this->producer->process($exchange);
        $start = time();
        while (true) {
            $this->connectionService->tick();
            if (count($this->consumerProcessor->getCaughtInMessages()) > 0) {
                return;
            }

            if (time() > $start + $timeout) {
                return;
            }
        }
    }
}
