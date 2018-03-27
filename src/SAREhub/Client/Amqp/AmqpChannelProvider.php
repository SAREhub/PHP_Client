<?php


namespace SAREhub\Client\Amqp;


use DI\Annotation\Inject;
use PhpAmqpLib\Connection\AbstractConnection;
use SAREhub\Commons\Logger\LoggerFactory;
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
     * @var LoggerFactory
     */
    private $loggerFactory;

    /**
     * @var int
     */
    private $hubId;

    /**
     * @var AmqpConsumer
     */
    private $consumer;

    /**
     * @Inject({"hubId" = "hubId"})
     * @param AbstractConnection $connection
     * @param LoggerFactory $loggerFactory
     * @param int $hubId
     * @param AmqpConsumer $consumer
     */
    public function __construct(AbstractConnection $connection, LoggerFactory $loggerFactory, int $hubId, AmqpConsumer $consumer)
    {
        $this->connection = $connection;
        $this->loggerFactory = $loggerFactory;
        $this->hubId = $hubId;
        $this->consumer = $consumer;
    }

    public function get()
    {
        $channel = new AmqpChannelWrapper($this->connection->channel());
        $channel->setPrefetchCountPerConsumer($this->getPrefetchCountFromEnv());

        $processConfirmStrategy = new BasicAmqpProcessConfirmStrategy();
        $processConfirmStrategy->setLogger($this->loggerFactory->create(self::ENTRY . ".processConfirmStrategy"));
        $processConfirmStrategy->setRejectRequeue(false);
        $channel->setProcessConfirmStrategy($processConfirmStrategy);

        $channel->registerConsumer($this->consumer);
        return $channel;
    }

    private function getPrefetchCountFromEnv(): int
    {
        return EnvironmentHelper::getVar(self::ENV_PREFETCH_COUNT, self::DEFAULT_PREFETCH_COUNT);
    }
}