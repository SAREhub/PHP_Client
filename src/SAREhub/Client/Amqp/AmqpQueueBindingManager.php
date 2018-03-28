<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpQueueBindingManager
{

    private $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    public function create(AmqpQueueBindingSchema $schema)
    {
        return $this->channel->queue_bind(
            $schema->getQueueName(),
            $schema->getExchangeName(),
            $schema->getRoutingKey()
        );
    }
}