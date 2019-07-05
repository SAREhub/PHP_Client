<?php


namespace SAREhub\Client\Amqp\Schema;


class AmqpQueueBindingSchema
{
    /**
     * @var string
     */
    private $queueName;

    /**
     * @var string
     */
    private $exchangeName;

    /**
     * @var string
     */
    private $routingKey;

    public static function newInstance(): self
    {
        return new self();
    }

    public function getQueueName(): string
    {
        return $this->queueName;
    }

    public function withQueueName(string $queueName): AmqpQueueBindingSchema
    {
        $this->queueName = $queueName;
        return $this;
    }

    public function getExchangeName(): string
    {
        return $this->exchangeName;
    }

    public function withExchangeName(string $exchangeName): AmqpQueueBindingSchema
    {
        $this->exchangeName = $exchangeName;
        return $this;
    }

    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    public function withRoutingKey(string $routingKey): AmqpQueueBindingSchema
    {
        $this->routingKey = $routingKey;
        return $this;
    }
}