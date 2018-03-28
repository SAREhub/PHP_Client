<?php


namespace SAREhub\Client\Amqp;


class AmqpEnvironmentSchema
{
    /**
     * @var AmqpQueueSchema[]
     */
    private $queueSchemas = [];

    /**
     * @var AmqpQueueBindingSchema[]
     */
    private $queueBindingSchemas = [];

    /**
     * @var AmqpExchangeSchema[]
     */
    private $exchangeSchemas = [];

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

    public function getQueueBindingSchemas(): array
    {
        return $this->queueBindingSchemas;
    }

    public function addQueueBindingSchema(AmqpQueueBindingSchema $queueBindingSchema): self
    {
        $this->queueBindingSchemas[] = $queueBindingSchema;
        return $this;
    }

    public function getExchangeSchemas(): array
    {
        return $this->exchangeSchemas;
    }

    public function addExchangeSchema(AmqpExchangeSchema $exchangeSchema): AmqpEnvironmentSchema
    {
        $this->exchangeSchemas[] = $exchangeSchema;
        return $this;
    }
}