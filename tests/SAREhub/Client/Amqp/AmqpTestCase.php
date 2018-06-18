<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PHPUnit\Framework\TestCase;

class AmqpTestCase extends TestCase
{
    /**
     * @var AbstractConnection
     */
    protected $connection;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    protected function setUp()
    {
        $this->connection = AmqpTestHelper::createConnection();
        $this->channel = $this->connection->channel();
    }
}