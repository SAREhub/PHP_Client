<?php


namespace SAREhub\Client\Amqp\Schema;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpQueueBindingManager
{
    const ERROR_MESSAGE = "Creating queue binding error(queue: %s; exchange: %s; routing_key: %s)";

    /**
     * @param AmqpQueueBindingSchema $schema
     * @param AMQPChannel $channel
     * @return bool
     * @throws AmqpSchemaException
     */
    public function create(AmqpQueueBindingSchema $schema, AMQPChannel $channel): bool
    {
        try {
            $channel->queue_bind(
                $schema->getQueueName(),
                $schema->getExchangeName(),
                $schema->getRoutingKey()
            );
            return true;
        } catch (\Exception $e) {
            throw new AmqpSchemaException(sprintf(
                self::ERROR_MESSAGE,
                $schema->getQueueName(),
                $schema->getExchangeName(),
                $schema->getRoutingKey()
            ), $e);
        }
    }
}