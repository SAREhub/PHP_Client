<?php

namespace SAREhub\Client\Amqp;

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
        $connection = AmqpTestHelper::createConnection();
        $schema = AmqpEnvironmentSchema::newInstance()
            ->addQueueSchema(AmqpQueueSchema::newInstance()->withName($this->queueName)->withAutoDelete(true));
        $this->connectionService = new AmqpConnectionService($connection);
        $createSchemaTask = new AmqpEnvironmentSchemaCreator(new AmqpEnvironmentManager(), $schema);
        $this->channel = $this->connectionService->createChannel("main", $createSchemaTask);
        $this->consumerProcessor = new ExchangeCatcherProcessor();
        $opts = AmqpConsumerOptions::newInstance()->setQueueName($this->queueName);
        $this->channel->registerConsumer(new AmqpConsumer($opts, $this->consumerProcessor));
        $this->producer = new AmqpProducer($this->channel);
        $this->connectionService->start();
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

    public function testReconnect()
    {
        $this->connectionService->close();
        $this->connectionService->reconnect();
        $this->connectionService->tick();
        $publishedMessage = BasicMessage::newInstance()
            ->setBody("test_" . mt_rand(1, 100000))
            ->setHeader(AMH::ROUTING_KEY, $this->queueName);
        $this->publishAndWaitForMessage($publishedMessage);
        $this->assertCount(1, $this->consumerProcessor->getCaughtInMessages());
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
