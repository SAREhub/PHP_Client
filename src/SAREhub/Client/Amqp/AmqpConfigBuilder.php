<?php

namespace SAREhub\Client\Amqp;

use SAREhub\Commons\Misc\Parameters;

/**
 * Helper class for build AMQP connection config
 */
class AmqpConfigBuilder
{

    private $host = '127.0.0.1';
    private $port = 5672;
    private $vhost = 'SAREhub';
    private $username = '';
    private $password = '';

    private $heartbeat = 30;
    private $keepalive = true;

    /**
     * @param string $host
     * @return $this
     */
    public function host($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param int $port
     * @return $this
     */
    public function port($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function username($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function password($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param string $vhost
     * @return $this
     */
    public function vhost($vhost)
    {
        $this->vhost = $vhost;
        return $this;
    }

    /**
     * @param int $heartbeat
     * @return $this
     */
    public function heartbeat($heartbeat)
    {
        $this->heartbeat = $heartbeat;
        return $this;
    }

    /**
     * @param boolean $keepalive
     */
    public function keepalive($keepalive)
    {
        $this->keepalive = $keepalive;
    }

    /**
     * @return Parameters
     */
    public function build()
    {
        return new Parameters(get_object_vars($this));
    }

}