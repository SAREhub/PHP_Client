<?php


namespace SAREhub\Client\Amqp;


class AmqpEnvironmentSchema
{
    /**
     * @var AmqpQueueSchema[]
     */
    private $queueSchemas = [];

    public static function newInstance(): self
    {
        return new self();
    }

    public function getQueueSchemas(): array
    {
        return $this->queueSchemas;
    }

    public function addQueueSchema(AmqpQueueSchema $queueSchema): AmqpEnvironmentSchema
    {
        $this->queueSchemas[] = $queueSchema;
        return $this;
    }


}