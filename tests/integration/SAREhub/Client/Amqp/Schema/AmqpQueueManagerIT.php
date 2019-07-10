<?php

namespace SAREhub\Client\Amqp\Schema;


use PhpAmqpLib\Wire\AMQPTable;
use SAREhub\Client\Amqp\AmqpTestCase;

class AmqpQueueManagerIT extends AmqpTestCase
{

    private $queueName = "AmqpQueueManagerIT";

    /**
     * @var AmqpQueueManager
     */
    private $queueManager;

    protected function setUp()
    {
        parent::setUp();
        $this->queueManager = new AmqpQueueManager();
        $this->channel->queue_delete($this->queueName);
    }

    /**
     * @throws AmqpSchemaException
     */
    public function testCreate()
    {
        $this->assertTrue($this->queueManager->create($this->createTestQueueSchema(), $this->channel));
    }

    /**
     * @depends testCreate
     * @throws AmqpSchemaException
     */
    public function testCreateWhenExistAndPassiveIsSetToFalse()
    {
        $schema = $this->createTestQueueSchema();

        $this->expectException(AmqpSchemaException::class);

        $this->queueManager->create($schema, $this->channel);
        $this->queueManager->create($schema->withAutoDelete(true), $this->channel);
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
