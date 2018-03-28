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
        $this->channel = $connection->channel();

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
     * @return AmqpQueueSchema
     */
    private function createTestQueueInfo(): AmqpQueueSchema
    {
        $queueInfo = AmqpQueueSchema::newInstance()
            ->withQueueName("AmqpQueueManagerIT")
            ->withPassive(false)
            ->withDurable(true)
            ->withExclusive(false)
            ->withAutoDelete(false)
            ->withArguments(new AMQPTable([]));

        return $queueInfo;
    }
}
