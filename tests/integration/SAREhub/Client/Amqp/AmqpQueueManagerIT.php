<?php

namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
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
        $this->channel = (new AmqpChannelWrapper($connection->channel()))->getWrappedChannel();

        $this->queueManager = new AmqpQueueManager($this->channel);
        $this->channel->queue_delete($this->queueName);
    }

    public function testCreate()
    {
        $queueInfo = $this->createTestQueueInfo();
        $this->assertEquals($this->queueName, $this->queueManager->create($queueInfo)[0]);
    }

    /**
     * @depends testCreate
     */
    public function testCreateWhenExist()
    {
        $queueInfo = $this->createTestQueueInfo();

        $this->expectException(AMQPProtocolChannelException::class);

        $this->queueManager->create($queueInfo);
        $this->queueManager->create($queueInfo->withAutoDelete(true));
    }

    /**
     * @depends testCreateWhenExist
     */
    public function testCreateWhenExchangeAndRoutingKeyAreSetThenCreateBinding()
    {
        $queueInfo = $this->createTestQueueInfo();

        $this->channel->exchange_declare('test', 'topic');

        $queueData = $this->queueManager->create($queueInfo
            ->withExchange('test')
            ->withRoutingKey('test')
        );

        $this->assertEquals($this->queueName, $queueData[0]);

        $this->channel->exchange_delete('test');
    }

    /**
     * @depends testCreateWhenExchangeAndRoutingKeyAreSetThenCreateBinding
     */
    public function testCreateWhenExchangeAndRoutingKeyAreSetAndExchangeNotExistThenCreateExchange()
    {
        $queueInfo = $this->createTestQueueInfo();

        $queueData = $this->queueManager->create($queueInfo
            ->withExchange('test')
            ->withRoutingKey('test')
        );

        $this->assertEquals($this->queueName, $queueData[0]);

        $this->channel->exchange_delete('test');
    }

    /**
     * @return AmqpQueueSchema
     */
    private function createTestQueueInfo(): AmqpQueueSchema
    {
        $queueInfo = AmqpQueueSchema::newInstance()
            ->withQueueName("AmqpQueueManagerIT")
            ->withPassive(false)
            ->withDurable(true)
            ->withExcelusive(false)
            ->withAutoDelete(false)
            ->withNowait(false)
            ->withArguments(new AMQPTable([]));

        return $queueInfo;
    }
}
