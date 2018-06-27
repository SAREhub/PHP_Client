<?php

namespace SAREhub\Client\Amqp\Task;


use SAREhub\Client\Amqp\AmqpEnvironmentManager;
use SAREhub\Client\Amqp\AmqpEnvironmentSchema;
use SAREhub\Client\Amqp\AmqpSchemaException;
use SAREhub\Commons\Task\Task;

class CreateAmqpSchemaTask implements Task
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

    /**
     * @throws AmqpSchemaException
     */
    public function run()
    {
        $this->manager->create($this->schema);
    }
}