<?php

namespace SAREhub\Client\Amqp;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PhpAmqpLib\Connection\AbstractConnection;
use PHPUnit\Framework\TestCase;

class AmqpConnectionProviderTest extends TestCase
{

    use MockeryPHPUnitIntegration;

    public function testGet()
    {
        $factory = \Mockery::mock(AmqpConnectionFactory::class);
        $options = new AmqpConnectionOptions();
        $provider = new AmqpConnectionProvider($factory, $options);

        $connection = \Mockery::mock(AbstractConnection::class);
        $factory->expects("create")->withArgs([$options])->andReturn($connection);
        $this->assertSame($connection, $provider->get());
    }
}
