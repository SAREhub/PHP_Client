<?php

namespace SAREhub\Client\Amqp\Task;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpEnvironmentManager;
use SAREhub\Client\Amqp\AmqpEnvironmentSchema;

class CreateAmqpSchemaTaskTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRun()
    {
        $schemaManager = \Mockery::mock(AmqpEnvironmentManager::class);
        $schema = new AmqpEnvironmentSchema();
        $task = new CreateAmqpSchemaTask($schemaManager, $schema);

        $schemaManager->expects("create")->with($schema);
        $task->run();
    }
}
