<?php


namespace SAREhub\Client\Amqp;


class AmqpEnvironmentSchema
{
    /**
     * @var AmqpQueueSchema[]
     */
    private $queueSchemaCollection = [];

    public static function newInstance(): self
    {
        return new self();
    }

    public function getQueueSchemaCollection(): array
    {
        return $this->queueSchemaCollection;
    }

    public function withQueueSchemaCollection(array $queueSchemasCollection): AmqpEnvironmentSchema
    {
        $this->queueSchemaCollection = $queueSchemasCollection;
        return $this;
    }

    public function addQueueSchema(AmqpQueueSchema $queueSchema): AmqpEnvironmentSchema
    {
        $this->queueSchemaCollection[] = $queueSchema;
        return $this;
    }


}