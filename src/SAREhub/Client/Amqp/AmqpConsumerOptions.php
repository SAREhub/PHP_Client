<?php

namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Wire\AMQPTable;

class AmqpConsumerOptions implements \JsonSerializable
{
    /**
     * @var string
     */
    private $queueName = '';

    /**
     * @var string
     */
    private $tag = '';

    /**
     * @var int
     */
    private $priority = 0;

    /**
     * @var bool
     */
    private $autoAck = false;

    /**
     * @var bool
     */
    private $exclusive = false;

    public static function newInstance(): AmqpConsumerOptions
    {
        return new self();
    }

    public function setQueueName(string $queueName): AmqpConsumerOptions
    {
        $this->queueName = $queueName;
        return $this;
    }

    public function setTag(string $tag): AmqpConsumerOptions
    {
        $this->tag = $tag;
        return $this;
    }

    public function setPriority(int $priority): AmqpConsumerOptions
    {
        $this->priority = $priority;
        return $this;
    }

    public function setAckMode(): AmqpConsumerOptions
    {
        $this->autoAck = false;
        return $this;
    }

    public function setAutoAckMode(): AmqpConsumerOptions
    {
        $this->autoAck = true;
        return $this;
    }

    public function setExclusive(bool $exclusive): AmqpConsumerOptions
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    public function getQueueName(): string
    {
        return $this->queueName;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isAutoAckMode(): bool
    {
        return $this->autoAck;
    }

    public function isAckMode(): bool
    {
        return !$this->autoAck;
    }

    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    public function getConsumeArguments(): AMQPTable
    {
        $args = [];
        if ($this->getPriority() > 0) {
            $args["x-priority"] = $this->getPriority();
        }
        return new AMQPTable($args);
    }

    public function jsonSerialize()
    {
        return [
            "queueName" => $this->getQueueName(),
            "tag" => $this->getTag(),
            "priority" => $this->getPriority(),
            "isAutoAck" => $this->isAutoAckMode(),
            "isExclusive" => $this->isExclusive()
        ];
    }
}