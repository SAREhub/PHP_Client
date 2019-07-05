<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;

class AmqpChannelWrapperState
{
    const DEFAULT_WAIT_TIMEOUT = 1;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var int
     */
    private $channelPrefetchCount = 0;

    /**
     * @var int
     */
    private $channelPrefetchSize = 0;

    /**
     * @var int
     */
    private $consumerPrefetchCount = 0;

    /**
     * @var int
     */
    private $consumerPrefetchSize = 0;

    /**
     * @var int
     */
    private $waitTimeout = self::DEFAULT_WAIT_TIMEOUT;

    /**
     * @var AmqpConsumer[]
     */
    private $consumers = [];

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function setChannel(AMQPChannel $channel): void
    {
        $this->channel = $channel;
    }

    public function getChannelPrefetchCount(): int
    {
        return $this->channelPrefetchCount;
    }

    public function getChannelPrefetchSize(): int
    {
        return $this->channelPrefetchSize;
    }

    public function setChannelPrefetch(int $count, int $size): void
    {
        $this->setChannelPrefetchCount($count);
        $this->setChannelPrefetchSize($size);
    }

    public function setChannelPrefetchCount(int $count): void
    {
        $this->channelPrefetchCount = $count;
    }

    public function setChannelPrefetchSize(int $size): void
    {
        $this->channelPrefetchSize = $size;
    }

    public function getConsumerPrefetchCount(): int
    {
        return $this->consumerPrefetchCount;
    }

    public function getConsumerPrefetchSize(): int
    {
        return $this->consumerPrefetchSize;
    }

    public function setConsumerPrefetch(int $count, int $size): void
    {
        $this->setConsumerPrefetchCount($count);
        $this->setConsumerPrefetchSize($size);
    }

    public function setConsumerPrefetchCount(int $count): void
    {
        $this->consumerPrefetchCount = $count;
    }

    public function setConsumerPrefetchSize(int $size): void
    {
        $this->consumerPrefetchSize = $size;
    }

    public function getWaitTimeout(): int
    {
        return $this->waitTimeout;
    }

    public function setWaitTimeout(int $waitTimeout): void
    {
        $this->waitTimeout = $waitTimeout;
    }

    public function addConsumer(AmqpConsumer $consumer): void
    {
        $this->consumers[] = $consumer;
    }

    public function removeConsumer(AmqpConsumer $consumer): void
    {
        foreach ($this->consumers as $index => $other) {
            if ($consumer === $other) {
                unset($this->consumers[$index]);
                return;
            }
        }
    }

    public function getConsumer(string $consumerTag): AmqpConsumer
    {
        foreach ($this->consumers as $index => $consumer) {
            if ($consumer->getTag() === $consumerTag) {
                return $consumer;
            }
        }

        throw new \InvalidArgumentException("consumer with tag: '$consumerTag' is not registered");
    }

    public function hasConsumer(string $consumerTag): bool
    {
        foreach ($this->consumers as $index => $consumer) {
            if ($consumer->getTag() === $consumerTag) {
                return true;
            }
        }

        return false;
    }

    public function getConsumers(): array
    {
        return $this->consumers;
    }

    public function setConsumers(array $consumers): void
    {
        $this->consumers = $consumers;
    }


}