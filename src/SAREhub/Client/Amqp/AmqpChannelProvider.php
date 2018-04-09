<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Connection\AbstractConnection;
use SAREhub\Commons\Misc\InvokableProvider;

class AmqpChannelProvider extends InvokableProvider
{
    /**
     * @var AbstractConnection
     */
    private $connection;

    /**
     * @param AbstractConnection $connection
     */
    public function __construct(AbstractConnection $connection)
    {
        $this->connection = $connection;
    }

    public function get()
    {
        return $this->connection->channel();
    }
}