<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpExchangeManager
{
    const EXCEPTION_MESSAGE_FORMAT = "AmqpExchangeManager occurred error when creating exchange (name: %s).";

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
            throw new AmqpSchemaException($this->getExceptionMessage($schema->getName()), $e);
        }
    }

    private function getExceptionMessage(string $exchangeName): string
    {
        return sprintf(self::EXCEPTION_MESSAGE_FORMAT, $exchangeName);
    }
}