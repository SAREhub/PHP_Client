<?php


namespace SAREhub\Client\Amqp\Schema;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpQueueManager
{
    const EXCEPTION_MESSAGE_FORMAT = "AmqpQueueManager occurred error when creating queue (name: %s)";

    /**
     * @param AmqpQueueSchema $schema
     * @param AMQPChannel $channel
     * @return bool
     * @throws AmqpSchemaException
     */
    public function create(AmqpQueueSchema $schema, AMQPChannel $channel): bool
    {
        try {
            $channel->queue_declare(
                $schema->getName(),
                $schema->isPassive(),
                $schema->isDurable(),
                $schema->isExclusive(),
                $schema->isAutoDelete(),
                false,
                $schema->getArguments()
            );
            return true;
        } catch (\Exception $e) {
            throw new AmqpSchemaException(sprintf(self::EXCEPTION_MESSAGE_FORMAT, ($schema->getName())), $e);
        }
    }
}