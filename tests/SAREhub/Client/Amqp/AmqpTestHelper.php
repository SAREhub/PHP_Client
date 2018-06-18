<?php

namespace SAREhub\Client\Amqp;


use SAREhub\Commons\Misc\EnvironmentHelper;

class AmqpTestHelper
{
    const ENV_HOST = "AMQP_HOST";
    const DEFAULT_HOST = "localhost";

    const ENV_PORT = "AMQP_PORT";
    const DEFAULT_PORT = 30000;

    const ENV_SSL_PORT = "AMQP_SSL_PORT";
    const DEFAULT_SSL_PORT = 30001;

    const CONNECTION_TIMEOUT = 20;

    public static function createConnection(bool $secure = true)
    {
        $connectionFactory = new AmqpConnectionFactory();
        return $connectionFactory->create(self::getConnectionOptions($secure));
    }

    private static function getConnectionOptions(bool $secure = true): AmqpConnectionOptions
    {
        $port = $secure ? EnvironmentHelper::getVar(self::ENV_SSL_PORT) : EnvironmentHelper::getVar(self::ENV_PORT);
        $host = EnvironmentHelper::getVar(self::ENV_HOST, self::DEFAULT_HOST);
        return AmqpConnectionOptions::newInstance()
            ->withHost($host)
            ->withPort($port)
            ->withVhost("test")
            ->withUser("test")
            ->withPassword("test")
            ->withSsl($secure)
            ->withConnectionTimeout(self::CONNECTION_TIMEOUT);
    }
}