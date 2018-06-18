<?php


namespace SAREhub\Client\Amqp;


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
        AmqpQueueManager $queueManager,
        AmqpQueueBindingManager $queueBindingManager,
        AmqpExchangeManager $exchangeManager
    )
    {
        $this->queueManager = $queueManager;
        $this->queueBindingManager = $queueBindingManager;
        $this->exchangeManager = $exchangeManager;
    }

    /**
     * @param AmqpEnvironmentSchema $environmentSchema
     * @throws AmqpSchemaException
     */
    public function create(AmqpEnvironmentSchema $environmentSchema)
    {
        $this->createQueues($environmentSchema);
        $this->createExchanges($environmentSchema);
        $this->createBindings($environmentSchema);
    }

    /**
     * @param AmqpEnvironmentSchema $environmentSchema
     * @throws AmqpSchemaException
     */
    private function createQueues(AmqpEnvironmentSchema $environmentSchema)
    {
        foreach ($environmentSchema->getQueueSchemas() as $schema) {
            $this->queueManager->create($schema);
        }
    }

    /**
     * @param AmqpEnvironmentSchema $environmentSchema
     * @throws AmqpSchemaException
     */
    private function createExchanges(AmqpEnvironmentSchema $environmentSchema)
    {
        foreach ($environmentSchema->getExchangeSchemas() as $schema) {
            $this->exchangeManager->create($schema);
        }
    }

    /**
     * @param AmqpEnvironmentSchema $environmentSchema
     * @throws AmqpSchemaException
     */
    private function createBindings(AmqpEnvironmentSchema $environmentSchema)
    {
        foreach ($environmentSchema->getQueueBindingSchemas() as $schema) {
            $this->queueBindingManager->create($schema);
        }

        foreach ($environmentSchema->getExchangeBindingSchemas() as $schema) {
            $this->exchangeManager->bindToExchange($schema);
        }
    }

}