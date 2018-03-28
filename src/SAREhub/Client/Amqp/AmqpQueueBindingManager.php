<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpQueueBindingManager
{

    const EXCEPTION_MESSAGE_FORMAT = "AmqpQueueBindingManager occurred error when creating binding (from: %s; to: %s; routing key: %s).";


    private $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @param AmqpQueueBindingSchema $schema
     * @return bool
     * @throws AmqpSchemaException
     */
    public function create(AmqpQueueBindingSchema $schema)
    {
        try {
            $this->channel->queue_bind(
                $schema->getQueueName(),
                $schema->getExchangeName(),
                $schema->getRoutingKey()
            );
            return true;
        } catch (\Exception $e) {
            throw new AmqpSchemaException($this->getExceptionMessage($schema), $e);
        }
    }

    private function getExceptionMessage(AmqpQueueBindingSchema $schema): string
    {
        return sprintf(
            self::EXCEPTION_MESSAGE_FORMAT,
            $schema->getQueueName(),
            $schema->getExchangeName(),
            $schema->getRoutingKey()
        );
    }
}