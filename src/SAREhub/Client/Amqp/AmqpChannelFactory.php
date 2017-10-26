<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;

class AmqpChannelFactory
{

    private $connection;

    public function __construct(AbstractConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return AmqpChannelWrapper
     */
    public function create()
    {
        return $this->createWrapper(new AMQPChannel($this->connection));
    }

    /**
     * @param AMQPChannel $channel
     * @return AmqpChannelWrapper
     */
    private function createWrapper(AMQPChannel $channel)
    {
        return new AmqpChannelWrapper($channel);
    }
}