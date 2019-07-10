<?php

namespace SAREhub\Client\Amqp;


use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PHPUnit\Framework\TestCase;

class AmqpConnectionServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MockInterface | AmqpConnectionProvider
     */
    private $connectionProvider;

    /**
     * @var AmqpConnectionService
     */
    private $service;

    protected function setUp()
    {
        $this->connectionProvider = \Mockery::mock(AmqpConnectionProvider::class);
        $this->service = new AmqpConnectionService($this->connectionProvider);
    }

    public function testStart()
    {
        $connection = $this->connectionProviderExpectsGet();
        $channelWrapper = \Mockery::mock(AmqpChannelWrapper::class);
        $this->service->addChannel($channelWrapper);

        $this->wrapperExpectsSetChannelFromConnection($channelWrapper, $connection);
        $channelWrapper->expects("start");

        $this->service->start();
    }

    public function testTick()
    {
        $connection = $this->connectionProviderExpectsGet();
        $channelWrapper = \Mockery::mock(AmqpChannelWrapper::class);
        $this->service->addChannel($channelWrapper);
        $this->wrapperExpectsSetChannelFromConnection($channelWrapper, $connection);
        $channelWrapper->expects("start");
        $this->service->start();

        $channelWrapper->expects("tick");

        $this->service->tick();
    }

    public function testTickWhenAmqpRuntimeExceptionThenReconnect()
    {
        $connection = $this->connectionProviderExpectsGet();
        $channelWrapper = \Mockery::mock(AmqpChannelWrapper::class);
        $this->service->addChannel($channelWrapper);
        $this->wrapperExpectsSetChannelFromConnection($channelWrapper, $connection);
        $channelWrapper->expects("start");
        $this->service->start();

        $channelWrapper->expects("tick")->andThrow(new AMQPRuntimeException("some AMQP error"));
        $connection = $this->connectionProviderExpectsGet();
        $this->wrapperExpectsSetChannelFromConnection($channelWrapper, $connection);
        $channelWrapper->expects("updateState");

        $this->service->tick();
    }

    public function testStop()
    {
        $connection = $this->connectionProviderExpectsGet();
        $channelWrapper = \Mockery::mock(AmqpChannelWrapper::class);
        $this->service->addChannel($channelWrapper);
        $this->wrapperExpectsSetChannelFromConnection($channelWrapper, $connection);
        $channelWrapper->expects("start");
        $this->service->start();

        $channelWrapper->expects("stop");
        $connection->expects("close");

        $this->service->stop();
    }

    /**
     * @return MockInterface | AbstractConnection
     */
    private function connectionProviderExpectsGet()
    {
        $connection = \Mockery::mock(AbstractConnection::class);
        $this->connectionProvider->expects("get")->andReturn($connection);
        return $connection;
    }

    private function wrapperExpectsSetChannelFromConnection(AmqpChannelWrapper $wrapper, AbstractConnection $connection)
    {
        $channel = \Mockery::mock(AMQPChannel::class);
        $connection->expects("channel")->andReturn($channel);
        $wrapper->expects("setWrappedChannel")->with($channel);
        return $channel;
    }
}
