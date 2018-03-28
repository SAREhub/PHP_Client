<?php

namespace SAREhub\Client\Amqp;


use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;

class AmqpEnvironmentManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testCreate()
    {
        /** @var Mock | AmqpQueueManager $queueManager */
        $queueManager = \Mockery::mock(AmqpQueueManager::class);

        $environmentSchema = new AmqpEnvironmentSchema();

        $queueSchema1 = AmqpQueueSchema::newInstance();
        $queueSchema2 = AmqpQueueSchema::newInstance();

        $environmentSchema->addQueueSchema($queueSchema1);
        $environmentSchema->addQueueSchema($queueSchema2);

        $queueManager->expects('create')->withArgs([$queueSchema1]);
        $queueManager->expects('create')->withArgs([$queueSchema2]);

        (new AmqpEnvironmentManager($queueManager))->create($environmentSchema);
    }
}
