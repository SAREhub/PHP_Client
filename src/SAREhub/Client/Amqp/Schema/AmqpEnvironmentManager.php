<?php


namespace SAREhub\Client\Amqp\Schema;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpEnvironmentManager
{
    /**
     * @var AmqpQueueManager
     */
    private $queueManager;

    /**
     * @var AmqpQueueBindingManager
     */
    private $queueBindingManager;

    /**
     * @var AmqpExchangeManager
     */
    private $exchangeManager;

    public function __construct(
        ?AmqpQueueManager $queueManager = null,
        ?AmqpQueueBindingManager $queueBindingManager = null,
        ?AmqpExchangeManager $exchangeManager = null
    )
    {
        $this->queueManager = $queueManager ?? new AmqpQueueManager();
        $this->queueBindingManager = $queueBindingManager ?? new AmqpQueueBindingManager();
        $this->exchangeManager = $exchangeManager ?? new AmqpExchangeManager();
    }

    /**
     * @param AmqpEnvironmentSchema $environmentSchema
     * @param AMQPChannel $channel
     * @throws AmqpSchemaException
     */
    public function create(AmqpEnvironmentSchema $environmentSchema, AMQPChannel $channel)
    {
        $this->createQueues($environmentSchema, $channel);
        $this->createExchanges($environmentSchema, $channel);
        $this->createBindings($environmentSchema, $channel);
    }

    /**
     * @param AmqpEnvironmentSchema $environmentSchema
     * @param AMQPChannel $channel
     * @throws AmqpSchemaException
     */
    private function createQueues(AmqpEnvironmentSchema $environmentSchema, AMQPChannel $channel)
    {
        foreach ($environmentSchema->getQueueSchemas() as $schema) {
            $this->queueManager->create($schema, $channel);
        }
    }

    /**
     * @param AmqpEnvironmentSchema $environmentSchema
     * @param AMQPChannel $channel
     * @throws AmqpSchemaException
     */
    private function createExchanges(AmqpEnvironmentSchema $environmentSchema, AMQPChannel $channel)
    {
        foreach ($environmentSchema->getExchangeSchemas() as $schema) {
            $this->exchangeManager->create($schema, $channel);
        }
    }

    /**
     * @param AmqpEnvironmentSchema $environmentSchema
     * @param AMQPChannel $channel
     * @throws AmqpSchemaException
     */
    private function createBindings(AmqpEnvironmentSchema $environmentSchema, AMQPChannel $channel)
    {
        foreach ($environmentSchema->getQueueBindingSchemas() as $schema) {
            $this->queueBindingManager->create($schema, $channel);
        }

        foreach ($environmentSchema->getExchangeBindingSchemas() as $schema) {
            $this->exchangeManager->bindToExchange($schema, $channel);
        }
    }

}