<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Connection\AbstractConnection;
use SAREhub\Commons\Misc\EnvironmentHelper;

class AmqpChannelProvider
{
    const ENTRY = "amqp.channel";

    const ENV_PREFETCH_COUNT = "AMQP_PREFETCH_COUNT";
    const DEFAULT_PREFETCH_COUNT = 3;

    const ENV_QUEUE_ARGUMENTS_EXPIRES = "AMQP_QUEUE_ARGUMENTS_EXPIRES";
    const DEFAULT_QUEUE_ARGUMENTS_EXPIRES = 7 * 24 * 60 * 60 * 1000; // 7 days in ms

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
        $channel = new AmqpChannelWrapper($this->connection->channel());
        $channel->setPrefetchCountPerConsumer($this->getPrefetchCountFromEnv());

        return $channel;
    }

    private function getPrefetchCountFromEnv(): int
    {
        return EnvironmentHelper::getVar(self::ENV_PREFETCH_COUNT, self::DEFAULT_PREFETCH_COUNT);
    }
}