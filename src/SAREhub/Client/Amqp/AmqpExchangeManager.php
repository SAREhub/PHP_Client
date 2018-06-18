<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;
use SAREhub\Client\Amqp\Schema\AmqpExchangeBindingSchema;

class AmqpExchangeManager
{
    const CREATE_ERROR = "Creating exchange error: %s";
    const BINDING_ERROR = "Creating exchange to exchange binding error(destination: %s; source: %s; routing_key: %s)";

    private $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @param AmqpExchangeSchema $schema
     * @return bool
     * @throws AmqpSchemaException
     */
    public function create(AmqpExchangeSchema $schema)
    {
        try {
            $this->channel->exchange_declare(
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
            $message = sprintf(self::CREATE_ERROR, $schema->getName());
            throw new AmqpSchemaException($message, $e);
        }
    }

    public function bindToExchange(AmqpExchangeBindingSchema $schema)
    {
        try {
            $this->channel->exchange_bind(
                $schema->getDestination(),
                $schema->getSource(),
                $schema->getRoutingKey(),
                false,
                $schema->getArguments()
            );
            return true;
        } catch (\Exception $e) {
            $message = sprintf(
                self::BINDING_ERROR,
                $schema->getDestination(),
                $schema->getSource(),
                $schema->getRoutingKey()
            );
            throw new AmqpSchemaException($message, $e);
        }
    }
}