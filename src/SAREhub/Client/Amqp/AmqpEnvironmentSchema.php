<?php


namespace SAREhub\Client\Amqp;


use SAREhub\Client\Amqp\Schema\AmqpExchangeBindingSchema;

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

    /**
     * @var AmqpExchangeBindingSchema[]
     */
    private $exchangeBindingSchemas = [];

    public static function newInstance(): self
    {
        return new self();
    }

    public function addQueueSchema(AmqpQueueSchema $schema): self
    {
        $this->queueSchemas[] = $schema;
        return $this;
    }

    public function addExchangeSchema(AmqpExchangeSchema $schema): self
    {
        $this->exchangeSchemas[] = $schema;
        return $this;
    }

    public function addQueueBindingSchema(AmqpQueueBindingSchema $schema): self
    {
        $this->queueBindingSchemas[] = $schema;
        return $this;
    }

    public function addExchangeBindingSchema(AmqpExchangeBindingSchema $schema): self
    {
        $this->exchangeBindingSchemas[] = $schema;
        return $this;
    }

    public function getQueueSchemas(): array
    {
        return $this->queueSchemas;
    }

    public function getExchangeSchemas(): array
    {
        return $this->exchangeSchemas;
    }

    public function getQueueBindingSchemas(): array
    {
        return $this->queueBindingSchemas;
    }

    public function getExchangeBindingSchemas(): array
    {
        return $this->exchangeBindingSchemas;
    }
}