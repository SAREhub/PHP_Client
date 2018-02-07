<?php

namespace SAREhub\Client\Amqp;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use SAREhub\Commons\Secret\SecretValueProvider;

class EnvAmqpConnectionOptionsProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var string
     */
    private $envVarPrefix;

    /**
     * @var Mock | SecretValueProvider
     */
    private $secretValueProvider;

    /**
     * @var AmqpConnectionOptionsProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->envVarPrefix = $this->getName();
        $this->secretValueProvider = \Mockery::mock(SecretValueProvider::class);
        $this->secretValueProvider->shouldIgnoreMissing("");
        $this->provider = new EnvAmqpConnectionOptionsProvider($this->envVarPrefix, $this->secretValueProvider);
    }

    /**
     * @dataProvider setsOptionProvider
     * @param string $expectedOptionValue
     * @param string $envVar
     * @param string $optionName
     */
    public function testGet($expectedOptionValue, string $envVar, string $optionName)
    {
        $this->putEnvVar($envVar, $expectedOptionValue);
        $options = $this->provider->get();
        $this->assertEquals($expectedOptionValue, $options->{$optionName}());
    }

    public function testGetWhenHasPasswordSecret()
    {
        $passwordSecret = "test_password_secret";
        $this->putEnvVar(EnvAmqpConnectionOptionsProvider::ENV_PASSWORD_SECRET, $passwordSecret);
        $this->secretValueProvider->expects("get")->withArgs([$passwordSecret])->andReturn("test_password");

        $options = $this->provider->get();
        $this->assertEquals("test_password", $options->getPassword());
    }

    public function setsOptionProvider()
    {
        return [
            "Host" => ["test_host", EnvAmqpConnectionOptionsProvider::ENV_HOST, "getHost"],
            "Vhost" => ["test_vhost", EnvAmqpConnectionOptionsProvider::ENV_VHOST, "getVhost"],
            "Port" => [10000, EnvAmqpConnectionOptionsProvider::ENV_PORT, "getPort"],
            "User" => ["test_user", EnvAmqpConnectionOptionsProvider::ENV_USER, "getUser"],
            "SslEnabled" => [true, EnvAmqpConnectionOptionsProvider::ENV_SSL_ENABLED, "isSslEnabled"],
            "SslVerifyPeer" => [true, EnvAmqpConnectionOptionsProvider::ENV_SSL_VERIFY_PEER, "isSslVerifyPeer"],
            "SslVerifyPeerName" => [true, EnvAmqpConnectionOptionsProvider::ENV_SSL_VERIFY_PEER_NAME, "isSslVerifyPeerName"],
            "ConnectionTimeout" => [100, EnvAmqpConnectionOptionsProvider::ENV_CONNECTION_TIMEOUT, "getConnectionTimeout"],
            "ReadWriteTimeout" => [100, EnvAmqpConnectionOptionsProvider::ENV_READ_WRITE_TIMEOUT, "getReadWriteTimeout"],
            "Keepalive" => [true, EnvAmqpConnectionOptionsProvider::ENV_KEEPALIVE, "isKeepalive"],
            "Heartbeat" => [100, EnvAmqpConnectionOptionsProvider::ENV_HEARTBEAT, "getHeartbeat"]
        ];
    }

    private function putEnvVar(string $name, $value)
    {
        $name = $this->envVarPrefix . $name;
        putenv("$name=$value");
    }
}
