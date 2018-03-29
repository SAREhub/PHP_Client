<?php

namespace SAREhub\Client\Amqp;


use PHPUnit\Framework\TestCase;
use SAREhub\Commons\Misc\EnvironmentHelper;

class AmqpConnectionFactoryIT extends TestCase
{
    /**
     * @var AmqpConnectionFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new AmqpConnectionFactory();
    }

    public function testCreateWithoutSsl()
    {
        $connection = $this->factory->create(AmqpConnectionOptions::newInstance()
            ->withHost(EnvironmentHelper::getVar("AMQP_HOST", 'localhost'))
            ->withPort((int)EnvironmentHelper::getVar("AMQP_PORT"))
            ->withVhost("test")
            ->withUser("test")
            ->withPassword("test")
            ->withConnectionTimeout(AmqpTestHelper::CONNECTION_TIMEOUT)
        );

        $this->assertTrue($connection->isConnected());
        $connection->close();
    }

    public function testCreateWithSsl()
    {
        $connection = $this->factory->create(AmqpConnectionOptions::newInstance()
            ->withHost(EnvironmentHelper::getVar("AMQP_HOST", 'localhost'))
            ->withPort(EnvironmentHelper::getVar("AMQP_SSL_PORT"))
            ->withVhost("test")
            ->withUser("test")
            ->withPassword("test")
            ->withSsl()
            ->withConnectionTimeout(AmqpTestHelper::CONNECTION_TIMEOUT)
        );
        $this->assertTrue($connection->isConnected());
        $connection->close();
    }
}