<?php


namespace SAREhub\Client\Amqp\Schema;


use PhpAmqpLib\Channel\AMQPChannel;
use SAREhub\Client\Amqp\Schema\AmqpExchangeBindingSchema;

class AmqpExchangeManager
{
    const CREATE_ERROR = "Creating exchange error: %s";
    const BINDING_ERROR = "Creating exchange to exchange binding error(destination: %s; source: %s; routing_key: %s)";

    /**
     * @param AmqpExchangeSchema $schema
     * @param AMQPChannel $channel
     * @return bool
     * @throws AmqpSchemaException
     */
    public function create(AmqpExchangeSchema $schema, AMQPChannel $channel)
    {
        try {
            $channel->exchange_declare(
                $schema->getName(),
                $schema->getType(),
                $schema->isPassive(),
                $schema->isDurable(),
                $schema->isAutoDeletable(),
                $schema->isInternal(),
                false,
                $schema->getArguments()
            );
            return true;
        } catch (\Exception $e) {
            throw new AmqpSchemaException(sprintf(self::CREATE_ERROR, $schema->getName()), $e);
        }
    }

    /**
     * @param AmqpExchangeBindingSchema $schema
     * @param AMQPChannel $channel
     * @return bool
     * @throws AmqpSchemaException
     */
    public function bindToExchange(AmqpExchangeBindingSchema $schema, AMQPChannel $channel): bool
    {
        try {
            $channel->exchange_bind(
                $schema->getDestination(),
                $schema->getSource(),
                $schema->getRoutingKey(),
                false,
                $schema->getArguments()
            );
            return true;
        } catch (\Exception $e) {
            throw new AmqpSchemaException(sprintf(
                self::BINDING_ERROR,
                $schema->getDestination(),
                $schema->getSource(),
                $schema->getRoutingKey()
            ), $e);
        }
    }
}