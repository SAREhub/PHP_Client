<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Wire\AMQPTable;

class AmqpExchangeSchema
{
    /**
     * @var string 
     */
    private $exchangeName = "";

    /**
     * @var string 
     */
    private $type = "topic";

    /**
     * @var bool 
     */
    private $passive = false;

    /**
     * @var bool 
     */
    private $durable = true;

    /**
     * @var bool
     */
    private $autoDelete = false;

    /**
     * @var bool
     */
    private $internal = false;

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

    public function getExchangeName(): string
    {
        return $this->exchangeName;
    }

    public function withExchangeName(string $exchangeName): AmqpExchangeSchema
    {
        $this->exchangeName = $exchangeName;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function withType(string $type): AmqpExchangeSchema
    {
        $this->type = $type;
        return $this;
    }

    public function isPassive(): bool
    {
        return $this->passive;
    }

    public function withPassive(bool $passive): AmqpExchangeSchema
    {
        $this->passive = $passive;
        return $this;
    }

    public function isDurable(): bool
    {
        return $this->durable;
    }

    public function withDurable(bool $durable): AmqpExchangeSchema
    {
        $this->durable = $durable;
        return $this;
    }

    public function isAutoDelete(): bool
    {
        return $this->autoDelete;
    }

    public function withAutoDelete(bool $autoDelete): AmqpExchangeSchema
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    public function isInternal(): bool
    {
        return $this->internal;
    }

    public function withInternal(bool $internal): AmqpExchangeSchema
    {
        $this->internal = $internal;
        return $this;
    }

    public function getArguments(): AMQPTable
    {
        return $this->arguments;
    }

    public function withArguments(AMQPTable $arguments): AmqpExchangeSchema
    {
        $this->arguments = $arguments;
        return $this;
    }

}