<?php

namespace SAREhub\Client\Amqp;


class AmqpConnectionOptions
{
    const DEFAULT_CONNECTION_TIMEOUT = 3;
    const DEFAULT_HEARTBEAT = 30;

    /**
     * @var string
     */
    private $host = "";

    /**
     * @var string
     */
    private $vhost = "/";

    /**
     * @var int
     */
    private $port = 5672;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $connectionTimeout = self::DEFAULT_CONNECTION_TIMEOUT;

    /**
     * @var int
     */
    private $readWriteTimeout = (self::DEFAULT_HEARTBEAT * 2) + 1;

    /**
     * @var bool
     */
    private $keepalive = true;

    /**
     * @var int
     */
    private $heartbeat = self::DEFAULT_HEARTBEAT;

    /**
     * @var bool
     */
    private $sslEnabled = false;

    /**
     * @var bool
     */
    private $sslVerifyPeer = false;

    /**
     * @var bool
     */
    private $sslVerifyPeerName = false;

    public static function newInstance(): AmqpConnectionOptions
    {
        return new self();
    }

    public function withHost(string $host): AmqpConnectionOptions
    {
        $this->host = $host;
        return $this;
    }

    public function withVhost(string $vhost): AmqpConnectionOptions
    {
        $this->vhost = $vhost;
        return $this;
    }

    public function withPort(int $port): AmqpConnectionOptions
    {
        $this->port = $port;
        return $this;
    }

    public function withUser(string $user): AmqpConnectionOptions
    {
        $this->user = $user;
        return $this;
    }

    public function withPassword(string $password): AmqpConnectionOptions
    {
        $this->password = $password;
        return $this;
    }

    public function withConnectionTimeout(int $connectionTimeout): AmqpConnectionOptions
    {
        $this->connectionTimeout = $connectionTimeout;
        return $this;
    }

    public function withReadWriteTimeout(int $readWriteTimeout): AmqpConnectionOptions
    {
        $this->readWriteTimeout = $readWriteTimeout;
        return $this;
    }

    public function withKeepalive(bool $keepalive = true): AmqpConnectionOptions
    {
        $this->keepalive = $keepalive;
        return $this;
    }

    public function withHeartbeat(int $heartbeat): AmqpConnectionOptions
    {
        $this->heartbeat = $heartbeat;
        return $this;
    }

    public function withSsl(bool $enabled = true): AmqpConnectionOptions
    {
        $this->sslEnabled = $enabled;
        return $this;
    }

    public function withSslVerifyPeer(bool $sslVerifyPeer): AmqpConnectionOptions
    {
        $this->sslVerifyPeer = $sslVerifyPeer;
        return $this;
    }


    public function withSslVerifyPeerName(bool $sslVerifyPeerName): AmqpConnectionOptions
    {
        $this->sslVerifyPeerName = $sslVerifyPeerName;
        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getVhost(): string
    {
        return $this->vhost;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getConnectionTimeout(): int
    {
        return $this->connectionTimeout;
    }

    public function getReadWriteTimeout(): int
    {
        return $this->readWriteTimeout;
    }

    public function isKeepalive(): bool
    {
        return $this->keepalive;
    }

    public function getHeartbeat(): int
    {
        return $this->heartbeat;
    }

    public function isSslEnabled(): bool
    {
        return $this->sslEnabled;
    }

    public function isSslVerifyPeer(): bool
    {
        return $this->sslVerifyPeer;
    }

    public function isSslVerifyPeerName(): bool
    {
        return $this->sslVerifyPeerName;
    }
}