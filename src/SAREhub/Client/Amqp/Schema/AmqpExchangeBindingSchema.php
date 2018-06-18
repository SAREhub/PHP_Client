<?php

namespace SAREhub\Client\Amqp\Schema;


use PhpAmqpLib\Wire\AMQPTable;

class AmqpExchangeBindingSchema
{
    /**
     * @var string
     */
    private $destination;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $routingKey;

    /**
     * @var AMQPTable
     */
    private $arguments;

    public function __construct()
    {
        $this->arguments = new AMQPTable();
    }

    public static function newInstance(): self
    {
        return new self();
    }

    public function withDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    public function withSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function withRoutingKey(string $routingKey): self
    {
        $this->routingKey = $routingKey;
        return $this;
    }

    public function withArguments(AMQPTable $arguments): self
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    public function getArguments(): AMQPTable
    {
        return $this->arguments;
    }

}
