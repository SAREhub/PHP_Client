<?php

namespace SAREhub\Client\Amqp\Schema;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PhpAmqpLib\Channel\AMQPChannel;
use PHPUnit\Framework\TestCase;

class CreateAmqpSchemaTaskTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRun()
    {
        $schemaManager = \Mockery::mock(AmqpEnvironmentManager::class);
        $schema = new AmqpEnvironmentSchema();
        $channel = \Mockery::mock(AMQPChannel::class);
        $task = new AmqpEnvironmentSchemaCreator($schemaManager, $schema);

        $schemaManager->expects("create")->with($schema, $channel);

        $task->create($channel);
    }
}
