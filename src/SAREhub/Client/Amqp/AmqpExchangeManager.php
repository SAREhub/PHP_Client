<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpExchangeManager
{
    private $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    public function create(AmqpExchangeSchema $schema)
    {
        return $this->channel->exchange_declare(
            $schema->getName(),
            $schema->getType(),
            $schema->isPassive(),
            $schema->isDurable(),
            $schema->isAutoDeletable(),
            $schema->isInternal(),
            false,
            $schema->getArguments()
        );
    }
}