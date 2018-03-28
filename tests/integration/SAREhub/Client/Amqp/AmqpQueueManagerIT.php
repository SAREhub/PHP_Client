<?php

namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;
use PHPUnit\Framework\TestCase;

class AmqpQueueManagerIT extends TestCase
{
    /**
     * @var AMQPChannel
     */
    private $channel;

    private $queueName = "AmqpQueueManagerIT";

    /**
     * @var AmqpQueueManager
     */
    private $queueManager;

    protected function setUp()
    {
        $connection = AmqpTestHelper::createConnection();
        $this->channel = $connection->channel();

        $this->queueManager = new AmqpQueueManager($this->channel);
        $this->channel->queue_delete($this->queueName);
    }

    /**
     * @throws AmqpSchemaException
     */
    public function testCreate()
    {
        $this->assertTrue($this->queueManager->create($this->createTestQueueSchema()));
    }

    /**
     * @depends testCreate
     * @throws AmqpSchemaException
     */
    public function testCreateWhenExistAndPassiveIsSetToFalse()
    {
        $queueInfo = $this->createTestQueueSchema();

        $this->expectException(AmqpSchemaException::class);

        $this->queueManager->create($queueInfo);
        $this->queueManager->create($queueInfo->withAutoDelete(true));
    }

    /**
     * @return AmqpQueueSchema
     */
    private function createTestQueueSchema(): AmqpQueueSchema
    {
        return AmqpQueueSchema::newInstance()
            ->withName("AmqpQueueManagerIT")
            ->withPassive(false)
            ->withDurable(true)
            ->withExclusive(false)
            ->withAutoDelete(false)
            ->withArguments(new AMQPTable([]));
    }
}
