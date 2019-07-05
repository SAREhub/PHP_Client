<?php

namespace SAREhub\Client\Amqp\Schema;


use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use Mockery\MockInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PHPUnit\Framework\TestCase;

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

    /**
     * @var AMQPChannel | MockInterface
     */
    private $channel;

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

        $this->channel = \Mockery::mock(AMQPChannel::class);
    }

    public function testCreateWhenQueueSchemaAdded()
    {
        $environmentSchema = new AmqpEnvironmentSchema();

        $queueSchema1 = AmqpQueueSchema::newInstance();
        $queueSchema2 = AmqpQueueSchema::newInstance();

        $environmentSchema->addQueueSchema($queueSchema1);
        $environmentSchema->addQueueSchema($queueSchema2);

        $this->queueManager->expects("create")->with($queueSchema1, $this->channel);
        $this->queueManager->expects("create")->with($queueSchema2, $this->channel);

        $this->environmentManager->create($environmentSchema, $this->channel);
    }

    public function testCreateWhenQueueBindingSchemaAdded()
    {
        $environmentSchema = new AmqpEnvironmentSchema();

        $queueBindingSchema1 = AmqpQueueBindingSchema::newInstance();
        $queueBindingSchema2 = AmqpQueueBindingSchema::newInstance();

        $environmentSchema->addQueueBindingSchema($queueBindingSchema1);
        $environmentSchema->addQueueBindingSchema($queueBindingSchema2);

        $this->queueBindingManager->expects("create")->with($queueBindingSchema1, $this->channel);
        $this->queueBindingManager->expects("create")->with($queueBindingSchema2, $this->channel);

        $this->environmentManager->create($environmentSchema, $this->channel);
    }

    public function testCreateWhenExchangeSchemaAdded()
    {
        $environmentSchema = new AmqpEnvironmentSchema();

        $exchangeSchema1 = AmqpExchangeSchema::newInstance();
        $exchangeSchema2 = AmqpExchangeSchema::newInstance();

        $environmentSchema->addExchangeSchema($exchangeSchema1);
        $environmentSchema->addExchangeSchema($exchangeSchema2);

        $this->exchangeManager->expects("create")->with($exchangeSchema1, $this->channel);
        $this->exchangeManager->expects("create")->with($exchangeSchema2, $this->channel);

        $this->environmentManager->create($environmentSchema, $this->channel);
    }

    public function testCreateWhenExchangeBindingSchemaAdded()
    {
        $environmentSchema = new AmqpEnvironmentSchema();

        $schema1 = AmqpExchangeBindingSchema::newInstance();
        $schema2 = AmqpExchangeBindingSchema::newInstance();

        $environmentSchema->addExchangeBindingSchema($schema1);
        $environmentSchema->addExchangeBindingSchema($schema2);

        $this->exchangeManager->expects("bindToExchange")->with($schema1, $this->channel);
        $this->exchangeManager->expects("bindToExchange")->with($schema2, $this->channel);

        $this->environmentManager->create($environmentSchema, $this->channel);
    }
}
