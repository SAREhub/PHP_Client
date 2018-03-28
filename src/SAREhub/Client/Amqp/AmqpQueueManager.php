<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpQueueManager
{
    const EXCEPTION_MESSAGE_FORMAT = "AmqpQueueManager occurred error when creating queue (name: %s).";

    private $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @param AmqpQueueSchema $schema
     * @return bool
     * @throws AmqpSchemaException
     */
    public function create(AmqpQueueSchema $schema)
    {
        try {
            $this->channel->queue_declare(
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
            throw new AmqpSchemaException($this->getExceptionMessage($schema->getName()), $e);
        }
    }

    private function getExceptionMessage(string $queueName): string
    {
        return sprintf(self::EXCEPTION_MESSAGE_FORMAT, $queueName);
    }
}