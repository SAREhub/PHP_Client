<?php


namespace SAREhub\Client\Amqp;


use SAREhub\Commons\Misc\EnvironmentHelper;
use SAREhub\Commons\Misc\InvokableProvider;
use SAREhub\Commons\Secret\SecretValueProvider;

class EnvAmqpConnectionOptionsProvider extends InvokableProvider
{
    const ENV_HOST = "HOST";
    const ENV_PORT = "PORT";
    const ENV_VHOST = "VHOST";

    const ENV_USER = "USER";
    const ENV_PASSWORD_SECRET = "PASSWORD_SECRET";

    const ENV_SSL_ENABLED = "SSL_ENABLED";
    const ENV_SSL_VERIFY_PEER = "SSL_VERIFY_PEER";
    const ENV_SSL_VERIFY_PEER_NAME = "SSL_VERIFY_PEER_NAME";

    const ENV_CONNECTION_TIMEOUT = "CONNECTION_TIMEOUT";
    const ENV_READ_WRITE_TIMEOUT = "READ_WRITE_TIMEOUT";

    const ENV_KEEPALIVE = "KEEPALIVE";
    const ENV_HEARTBEAT = "HEARTBEAT";

    const ENV_SCHEMA = [
        self::ENV_HOST => "",
        self::ENV_PORT => 5671,
        self::ENV_VHOST => "",
        self::ENV_USER => "",
        self::ENV_PASSWORD_SECRET => "",

        self::ENV_SSL_ENABLED => true,
        self::ENV_SSL_VERIFY_PEER => false,
        self::ENV_SSL_VERIFY_PEER_NAME => false,

        self::ENV_CONNECTION_TIMEOUT => AmqpConnectionOptions::DEFAULT_CONNECTION_TIMEOUT,
        self::ENV_READ_WRITE_TIMEOUT => (AmqpConnectionOptions::DEFAULT_HEARTBEAT * 2) + 1,
        self::ENV_KEEPALIVE => false,
        self::ENV_HEARTBEAT => AmqpConnectionOptions::DEFAULT_HEARTBEAT
    ];

    const DEFAULT_ENV_VAR_PREFIX = "AMQP_";

    /**
     * @var string
     */
    private $envVarPrefix;

    /**
     * @var array
     */
    private $envSchema;

    /**
     * @var SecretValueProvider
     */
    private $secretValueProvider;

    public function __construct(
        SecretValueProvider $secretValueProvider,
        $envVarPrefix = self::DEFAULT_ENV_VAR_PREFIX,
        array $envSchema = []
    )
    {
        $this->envVarPrefix = $envVarPrefix;
        $this->envSchema = array_merge(self::ENV_SCHEMA, $envSchema);
        $this->secretValueProvider = $secretValueProvider;
    }

    /**
     * @return AmqpConnectionOptions
     * @throws \SAREhub\Commons\Secret\SecretValueNotFoundException
     */
    public function get(): AmqpConnectionOptions
    {
        $env = EnvironmentHelper::getVars($this->envSchema, $this->envVarPrefix);

        return AmqpConnectionOptions::newInstance()
            ->withHost($env[self::ENV_HOST])
            ->withVhost($env[self::ENV_VHOST])
            ->withPort($env[self::ENV_PORT])
            ->withUser($env[self::ENV_USER])
            ->withPassword($this->secretValueProvider->get($env[self::ENV_PASSWORD_SECRET]))
            ->withSsl((bool)$env[self::ENV_SSL_ENABLED])
            ->withSslVerifyPeer((bool)$env[self::ENV_SSL_VERIFY_PEER])
            ->withSslVerifyPeerName((bool)$env[self::ENV_SSL_VERIFY_PEER_NAME])
            ->withConnectionTimeout($env[self::ENV_CONNECTION_TIMEOUT])
            ->withReadWriteTimeout($env[self::ENV_READ_WRITE_TIMEOUT])
            ->withHeartbeat($env[self::ENV_HEARTBEAT]);
    }
}