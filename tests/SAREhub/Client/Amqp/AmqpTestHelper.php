<?php

namespace SAREhub\Client\Amqp;


use SAREhub\Commons\Misc\EnvironmentHelper;

class AmqpTestHelper
{
    const ENV_HOST = "AMQP_HOST";
    const ENV_PORT = "AMQP_PORT";
    const ENV_SSL_PORT = "AMQP_SSL_PORT";

    const CONNECTION_TIMEOUT = 20;

    public static function createConnection(bool $secure = true)
    {
        $connectionFactory = new AmqpConnectionFactory();
        return $connectionFactory->create(AmqpConnectionOptions::newInstance()
            ->withHost(EnvironmentHelper::getVar(self::ENV_HOST))
            ->withPort($secure ?
                EnvironmentHelper::getVar(self::ENV_SSL_PORT) :
                EnvironmentHelper::getVar(self::ENV_PORT))
            ->withVhost("test")
            ->withUser("test")
            ->withPassword("test")
            ->withSsl($secure)
            ->withConnectionTimeout(self::CONNECTION_TIMEOUT)
        );
    }
}