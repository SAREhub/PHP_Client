<?php


namespace SAREhub\Client\Amqp;


class AmqpEnvironmentSchema
{
    /**
     * @var AmqpQueueSchema[]
     */
    private $queueSchemasCollection = [];

    public static function newInstance(): self
    {
        return new self();
    }

    public function getQueueSchemasCollection(): array
    {
        return $this->queueSchemasCollection;
    }

    public function withQueueSchemasCollection(array $queueSchemasCollection): AmqpEnvironmentSchema
    {
        $this->queueSchemasCollection = $queueSchemasCollection;
        return $this;
    }

    public function addQueueSchema(AmqpQueueSchema $queueSchema): AmqpEnvironmentSchema
    {
        $this->queueSchemasCollection[] = $queueSchema;
        return $this;
    }


}