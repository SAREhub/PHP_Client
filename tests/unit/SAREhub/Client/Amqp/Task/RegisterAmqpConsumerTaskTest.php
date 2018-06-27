<?php

namespace SAREhub\Client\Amqp\Task;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpChannelWrapper;
use SAREhub\Client\Amqp\AmqpConsumer;

class RegisterAmqpConsumerTaskTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRun()
    {
        $channel = \Mockery::mock(AmqpChannelWrapper::class);
        $consumer = \Mockery::mock(AmqpConsumer::class);
        $task = new RegisterAmqpConsumerTask($channel, $consumer);

        $channel->expects("registerConsumer")->with($consumer);
        $task->run();
    }
}
