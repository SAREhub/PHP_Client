<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Wire\AMQPTable;

class AmqpQueueSchema
{
    /**
     * @var string
     */
    private $queueName;

    /**
     * @var string
     */
    private $exchange = '';

    /**
     * @var string
     */
    private $routingKey = '';

    /**
     * @var bool
     */
    private $passive;

    /**
     * @var bool
     */
    private $durable;

    /**
     * @var bool
     */
    private $exclusive;

    /**
     * @var bool
     */
    private $autoDelete;

    /**
     * @var bool
     */
    private $nowait = false;

    /**
     * @var AMQPTable
     */
    private $arguments;

    public static function newInstance(): self
    {
        return new self();
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

    public function getExchange(): string
    {
        return $this->exchange;
    }

    /**
     * @param string $exchange
     * @return AmqpQueueSchema
     */
    public function withExchange(string $exchange): AmqpQueueSchema
    {
        $this->exchange = $exchange;
        return $this;
    }

    public function isPassive(): bool
    {
        return $this->passive;
    }

    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    /**
     * @param string $routingKey
     * @return AmqpQueueSchema
     */
    public function withRoutingKey(string $routingKey): AmqpQueueSchema
    {
        $this->routingKey = $routingKey;
        return $this;
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
    public function withExclusive(bool $excelusive): AmqpQueueSchema
    {
        $this->exclusive = $excelusive;
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

    public function isNowait(): bool
    {
        return $this->nowait;
    }

    /**
     * @param bool $nowait
     * @return AmqpQueueSchema
     */
    public function withNowait(bool $nowait): AmqpQueueSchema
    {
        $this->nowait = $nowait;
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