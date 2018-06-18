<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpQueueBindingManager
{
    const ERROR_MESSAGE = "Creating queue binding error(queue: %s; exchange: %s; routing_key: %s)";

    /**
     * @var AMQPChannel
     */
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
            throw new AmqpSchemaException($this->formatExceptionMessage($schema), $e);
        }
    }

    private function formatExceptionMessage(AmqpQueueBindingSchema $schema): string
    {
        return sprintf(
            self::ERROR_MESSAGE,
            $schema->getQueueName(),
            $schema->getExchangeName(),
            $schema->getRoutingKey()
        );
    }
}