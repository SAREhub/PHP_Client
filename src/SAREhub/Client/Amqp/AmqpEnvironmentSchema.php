<?php


namespace SAREhub\Client\Amqp;


class AmqpEnvironmentSchema
{
    /**
     * @var AmqpQueueSchema[]
     */
    private $queueSchema = [];

    public static function newInstance(): self
    {
        return new self();
    }

    public function getQueueSchema(): array
    {
        return $this->queueSchema;
    }

    public function withQueueSchemaCollection(array $queueSchemas): AmqpEnvironmentSchema
    {
        $this->queueSchema = $queueSchemas;
        return $this;
    }

    public function addQueueSchema(AmqpQueueSchema $queueSchema): AmqpEnvironmentSchema
    {
        $this->queueSchema[] = $queueSchema;
        return $this;
    }


}