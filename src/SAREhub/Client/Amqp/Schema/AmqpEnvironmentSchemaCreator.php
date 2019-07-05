<?php

namespace SAREhub\Client\Amqp\Schema;


use PhpAmqpLib\Channel\AMQPChannel;
use SAREhub\Client\Amqp\Schema\AmqpEnvironmentManager;
use SAREhub\Client\Amqp\Schema\AmqpEnvironmentSchema;

class AmqpEnvironmentSchemaCreator
{
    /**
     * @var AmqpEnvironmentManager
     */
    private $manager;

    /**
     * @var AmqpEnvironmentSchema
     */
    private $schema;

    public function __construct(AmqpEnvironmentManager $manager, AmqpEnvironmentSchema $schema)
    {
        $this->manager = $manager;
        $this->schema = $schema;
    }

    public function create(AmqpChannel $channel): void
    {
        $this->manager->create($this->schema, $channel);
    }

}