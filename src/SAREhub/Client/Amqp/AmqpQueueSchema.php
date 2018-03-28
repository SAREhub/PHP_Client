<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Wire\AMQPTable;

class AmqpQueueSchema
{
    /**
     * @var string
     */
    private $queueName = "";

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

    public function getQueueName(): string
    {
        return $this->queueName;
    }

    /**
     * @param string $queueName
     * @return AmqpQueueSchema
     */
    public function withQueueName(string $queueName): AmqpQueueSchema
    {
        $this->queueName = $queueName;
        return $this;
    }

    public function isPassive(): bool
    {
        return $this->passive;
    }

    /**
     * @param bool $passive
     * @return AmqpQueueSchema
     */
    public function withPassive(bool $passive): AmqpQueueSchema
    {
        $this->passive = $passive;
        return $this;
    }

    public function isDurable(): bool
    {
        return $this->durable;
    }

    /**
     * @param bool $durable
     * @return AmqpQueueSchema
     */
    public function withDurable(bool $durable): AmqpQueueSchema
    {
        $this->durable = $durable;
        return $this;
    }

    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    /**
     * @param bool $excelusive
     * @return AmqpQueueSchema
     */
    public function withExclusive(bool $exclusive): AmqpQueueSchema
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    public function isAutoDelete(): bool
    {
        return $this->autoDelete;
    }

    /**
     * @param bool $autoDelete
     * @return AmqpQueueSchema
     */
    public function withAutoDelete(bool $autoDelete): AmqpQueueSchema
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    public function getArguments(): AMQPTable
    {
        return $this->arguments;
    }

    /**
     * @param AMQPTable $arguments
     * @return AmqpQueueSchema
     */
    public function withArguments(AMQPTable $arguments): AmqpQueueSchema
    {
        $this->arguments = $arguments;
        return $this;
    }
}