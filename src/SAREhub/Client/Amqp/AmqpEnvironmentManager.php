<?php


namespace SAREhub\Client\Amqp;


class AmqpEnvironmentManager
{
    /**
     * @var AmqpQueueManager
     */
    private $amqpQueueManager;

    /**
     * @var AmqpQueueBindingManager
     */
    private $amqpQueueBindingManager;

    /**
     * @var AmqpExchangeManager
     */
    private $amqpExchangeManager;

    public function __construct(
        AmqpQueueManager $queueManager,
        AmqpQueueBindingManager $queueBindingManager,
        AmqpExchangeManager $exchangeManager
    )
    {
        $this->amqpQueueManager = $queueManager;
        $this->amqpQueueBindingManager = $queueBindingManager;
        $this->amqpExchangeManager = $exchangeManager;
    }

    /**
     * @param AmqpEnvironmentSchema $environmentSchema
     * @throws AmqpSchemaException
     */
    public function create(AmqpEnvironmentSchema $environmentSchema)
    {
        foreach ($environmentSchema->getQueueSchemas() as $queueSchema) {
            $this->amqpQueueManager->create($queueSchema);
        }

        foreach ($environmentSchema->getExchangeSchemas() as $exchangeSchema) {
            $this->amqpExchangeManager->create($exchangeSchema);
        }

        foreach ($environmentSchema->getQueueBindingSchemas() as $queueBindingSchema) {
            $this->amqpQueueBindingManager->create($queueBindingSchema);
        }
    }
}