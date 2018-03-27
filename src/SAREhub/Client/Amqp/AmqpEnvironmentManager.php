<?php


namespace SAREhub\Client\Amqp;


class AmqpEnvironmentManager
{
    /**
     * @var AmqpQueueManager
     */
    private $amqpQueueManager;

    public function __construct(AmqpQueueManager $queueManager)
    {
        $this->amqpQueueManager = $queueManager;
    }

    public function create(AmqpEnvironmentSchema $environmentSchema)
    {
        foreach ($environmentSchema->getQueueSchemasCollection() as $queueSchema) {
            $this->amqpQueueManager->create($queueSchema);
        }
    }
}