<?php

namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PHPUnit\Framework\TestCase;

class AmqpQueueBindingManagerIT extends TestCase
{
    /**
     * @var AMQPChannel
     */
    private $channel;

    private $queueName = "AmqpQueueBindingManagerIT";

    private $exchangeName = "test_exchange";

    private $routingKey = "test";

    /**
     * @var AmqpQueueBindingManager
     */
    private $queueBindingManager;


    protected function setUp()
    {
        $connection = AmqpTestHelper::createConnection();
        $this->channel = $connection->channel();

        $this->channel->queue_delete($this->queueName);
        $this->channel->exchange_delete($this->exchangeName);

        $this->queueBindingManager = new AmqpQueueBindingManager($this->channel);

        $this->channel->queue_declare($this->queueName);
        $this->channel->exchange_declare($this->exchangeName, 'topic');
    }

    public function testCreateWhenGivenGoodDataThenCreateBinding()
    {
        $this->assertNull($this->queueBindingManager->create($this->createTestQueueBindingSchema()->withExchangeName($this->exchangeName)));
    }

    /**
     * @depends testCreateWhenGivenGoodDataThenCreateBinding
     */
    public function testCreateWhenGivenCorruptedDataThenThrowException()
    {
        $this->expectException(AMQPExceptionInterface::class);
        $this->queueBindingManager->create($this->createTestQueueBindingSchema()->withExchangeName("notExistingExchange"));
    }

    public function createTestQueueBindingSchema(): AmqpQueueBindingSchema
    {
        return AmqpQueueBindingSchema::newInstance()->withQueueName($this->queueName)->withRoutingKey($this->routingKey);
    }
}
