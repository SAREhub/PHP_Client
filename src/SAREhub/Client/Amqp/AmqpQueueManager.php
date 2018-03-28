<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpQueueManager
{
    private $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    public function create(AmqpQueueSchema $schema)
    {
        $queueData = $this->channel->queue_declare(
            $schema->getQueueName(),
            $schema->isPassive(),
            $schema->isDurable(),
            $schema->isExclusive(),
            $schema->isAutoDelete(),
            false,
            $schema->getArguments()
        );

        return $queueData;
    }
}