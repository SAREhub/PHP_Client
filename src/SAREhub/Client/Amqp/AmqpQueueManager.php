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

    public function create(AmqpQueueSchema $amqpQueueInfo)
    {
        $queueData = $this->channel->queue_declare(
            $amqpQueueInfo->getQueueName(),
            $amqpQueueInfo->isPassive(),
            $amqpQueueInfo->isDurable(),
            $amqpQueueInfo->isExclusive(),
            $amqpQueueInfo->isAutoDelete(),
            $amqpQueueInfo->isNowait(),
            $amqpQueueInfo->getArguments()
        );

        if (!empty($amqpQueueInfo->getExchange()) && !empty($amqpQueueInfo->getRoutingKey())) {
            $this->channel->exchange_declare($amqpQueueInfo->getExchange(), 'topic');
            $this->channel->queue_bind($amqpQueueInfo->getQueueName(), $amqpQueueInfo->getExchange(), $amqpQueueInfo->getRoutingKey());
        }

        return $queueData;
    }
}