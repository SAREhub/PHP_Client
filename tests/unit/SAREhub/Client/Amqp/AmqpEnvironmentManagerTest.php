<?php

namespace SAREhub\Client\Amqp;


use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\Schema\AmqpExchangeBindingSchema;

class AmqpEnvironmentManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Mock | AmqpQueueManager
     */
    private $queueManager;

    /**
     * @var Mock | AmqpQueueBindingManager
     */
    private $queueBindingManager;

    /**
     * @var Mock | AmqpExchangeManager
     */
    private $exchangeManager;

    /**
     * @var AmqpEnvironmentManager
     */
    private $environmentManager;

    public function setUp()
    {
        $this->queueManager = \Mockery::mock(AmqpQueueManager::class);
        $this->queueBindingManager = \Mockery::mock(AmqpQueueBindingManager::class);
        $this->exchangeManager = \Mockery::mock(AmqpExchangeManager::class);

        $this->environmentManager = new AmqpEnvironmentManager(
            $this->queueManager,
            $this->queueBindingManager,
            $this->exchangeManager
        );
    }

    public function testCreateWhenQueueSchemaAdded()
    {
        $environmentSchema = new AmqpEnvironmentSchema();

        $queueSchema1 = AmqpQueueSchema::newInstance();
        $queueSchema2 = AmqpQueueSchema::newInstance();

        $environmentSchema->addQueueSchema($queueSchema1);
        $environmentSchema->addQueueSchema($queueSchema2);

        $this->queueManager->expects('create')->withArgs([$queueSchema1]);
        $this->queueManager->expects('create')->withArgs([$queueSchema2]);

        $this->environmentManager->create($environmentSchema);
    }

    public function testCreateWhenQueueBindingSchemaAdded()
    {
        $environmentSchema = new AmqpEnvironmentSchema();

        $queueBindingSchema1 = AmqpQueueBindingSchema::newInstance();
        $queueBindingSchema2 = AmqpQueueBindingSchema::newInstance();

        $environmentSchema->addQueueBindingSchema($queueBindingSchema1);
        $environmentSchema->addQueueBindingSchema($queueBindingSchema2);

        $this->queueBindingManager->expects('create')->withArgs([$queueBindingSchema1]);
        $this->queueBindingManager->expects('create')->withArgs([$queueBindingSchema2]);

        $this->environmentManager->create($environmentSchema);
    }

    public function testCreateWhenExchangeSchemaAdded()
    {
        $environmentSchema = new AmqpEnvironmentSchema();

        $exchangeSchema1 = AmqpExchangeSchema::newInstance();
        $exchangeSchema2 = AmqpExchangeSchema::newInstance();

        $environmentSchema->addExchangeSchema($exchangeSchema1);
        $environmentSchema->addExchangeSchema($exchangeSchema2);

        $this->exchangeManager->expects('create')->withArgs([$exchangeSchema1]);
        $this->exchangeManager->expects('create')->withArgs([$exchangeSchema2]);

        $this->environmentManager->create($environmentSchema);
    }

    public function testCreateWhenExchangeBindingSchemaAdded()
    {
        $environmentSchema = new AmqpEnvironmentSchema();

        $schema1 = AmqpExchangeBindingSchema::newInstance();
        $schema2 = AmqpExchangeBindingSchema::newInstance();

        $environmentSchema->addExchangeBindingSchema($schema1);
        $environmentSchema->addExchangeBindingSchema($schema2);

        $this->exchangeManager->expects('bindToExchange')->withArgs([$schema1]);
        $this->exchangeManager->expects('bindToExchange')->withArgs([$schema2]);

        $this->environmentManager->create($environmentSchema);
    }
}
