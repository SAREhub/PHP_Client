<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Wire\AMQPTable;

class AmqpQueueSchema
{
    /**
     * @var string
     */
    private $name = "";

    /**
     * @var bool
     */
    private $passive = false;

    /**
     * @var bool
     */
    private $durable = false;

    /**
     * @var bool
     */
    private $exclusive = false;

    /**
     * @var bool
     */
    private $autoDelete = true;

    /**
     * @var AMQPTable
     */
    private $arguments;

    public static function newInstance(): self
    {
        return new self();
    }

    public function __construct()
    {
        $this->arguments = new AMQPTable();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function withName(string $name): AmqpQueueSchema
    {
        $this->name = $name;
        return $this;
    }

    public function isPassive(): bool
    {
        return $this->passive;
    }

    public function withPassive(bool $passive): AmqpQueueSchema
    {
        $this->passive = $passive;
        return $this;
    }

    public function isDurable(): bool
    {
        return $this->durable;
    }

    public function withDurable(bool $durable): AmqpQueueSchema
    {
        $this->durable = $durable;
        return $this;
    }

    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    public function withExclusive(bool $exclusive): AmqpQueueSchema
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    public function isAutoDelete(): bool
    {
        return $this->autoDelete;
    }

    public function withAutoDelete(bool $autoDelete): AmqpQueueSchema
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    public function getArguments(): AMQPTable
    {
        return $this->arguments;
    }

    public function withArguments(AMQPTable $arguments): AmqpQueueSchema
    {
        $this->arguments = $arguments;
        return $this;
    }
}