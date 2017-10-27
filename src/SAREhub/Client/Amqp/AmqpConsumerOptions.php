<?php

namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Wire\AMQPTable;
use SAREhub\Client\Processor\Processor;

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

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var AmqpMessageProcessConfirmStrategy
     */
    private $processConfirmStrategy;

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

    public function setAckMode(bool $ack): AmqpConsumerOptions
    {
        $this->ackMode = $ack;
        return $this;
    }

    public function setExclusive(bool $exclusive): AmqpConsumerOptions
    {
        $this->exclusive = $exclusive;
        return $this;
    }


    public function setProcessor(Processor $processor): AmqpConsumerOptions
    {
        $this->processor = $processor;
        return $this;
    }

    public function setProcessConfirmStrategy(AmqpMessageProcessConfirmStrategy $strategy): AmqpConsumerOptions
    {
        $this->processConfirmStrategy = $strategy;
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

    public function isAutoAck(): bool
    {
        return $this->autoAck;
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

    public function getProcessor(): Processor
    {
        return $this->processor;
    }

    public function getProcessConfirmStrategy(): AmqpMessageProcessConfirmStrategy
    {
        return $this->processConfirmStrategy;
    }

    public function jsonSerialize()
    {
        return [
            "queueName" => $this->getQueueName(),
            "tag" => $this->getTag(),
            "priority" => $this->getPriority(),
            "isAutoAck" => $this->isAutoAck(),
            "isExclusive" => $this->isExclusive(),
            "processor" => $this->getProcessor()
        ];
    }
}