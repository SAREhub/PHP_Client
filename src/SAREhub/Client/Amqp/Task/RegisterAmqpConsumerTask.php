<?php

namespace SAREhub\Client\Amqp\Task;

use SAREhub\Client\Amqp\AmqpChannelWrapper;
use SAREhub\Client\Amqp\AmqpConsumer;
use SAREhub\Commons\Task\Task;


class RegisterAmqpConsumerTask implements Task
{
    /**
     * @var AmqpChannelWrapper
     */
    private $channel;

    /**
     * @var AmqpConsumer
     */
    private $consumer;

    public function __construct(AmqpChannelWrapper $channel, AmqpConsumer $consumer)
    {
        $this->channel = $channel;
        $this->consumer = $consumer;
    }

    public function run()
    {
        $this->channel->registerConsumer($this->consumer);
    }
}