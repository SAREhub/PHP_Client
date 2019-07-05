<?php

namespace SAREhub\Client\Amqp\Schema;


use SAREhub\Client\Amqp\AmqpTestCase;

class AmqpQueueBindingManagerIT extends AmqpTestCase
{
    private $queueName = "AmqpQueueBindingManagerIT";
    private $exchangeName = "test_exchange";
    private $routingKey = "test";

    /**
     * @var AmqpQueueBindingManager
     */
    private $queueBindingManager;

    protected function setUp()
    {
        parent::setUp();
        $this->queueBindingManager = new AmqpQueueBindingManager();

        $this->channel->queue_delete($this->queueName);
        $this->channel->exchange_delete($this->exchangeName);
        $this->channel->queue_declare($this->queueName);
        $this->channel->exchange_declare($this->exchangeName, 'topic');
    }

    /**
     * @throws AmqpSchemaException
     */
    public function testCreateWhenGivenGoodDataThenCreateBinding()
    {
        $schema = $this->createTestQueueBindingSchema()->withExchangeName($this->exchangeName);
        $this->assertTrue($this->queueBindingManager->create($schema, $this->channel));
    }

    /**
     * @depends testCreateWhenGivenGoodDataThenCreateBinding
     * @throws AmqpSchemaException
     */
    public function testCreateWhenGivenCorruptedDataThenThrowException()
    {
        $this->expectException(AmqpSchemaException::class);
        $schema = $this->createTestQueueBindingSchema()->withExchangeName("notExistingExchange");
        $this->queueBindingManager->create($schema, $this->channel);
    }

    public function createTestQueueBindingSchema(): AmqpQueueBindingSchema
    {
        return AmqpQueueBindingSchema::newInstance()
            ->withQueueName($this->queueName)
            ->withRoutingKey($this->routingKey);
    }
}
