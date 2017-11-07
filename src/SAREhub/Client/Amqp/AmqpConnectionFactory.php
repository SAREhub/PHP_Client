<?php

namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AmqpConnectionFactory
{
    public function create(AmqpConnectionOptions $options)
    {
        return $options->isSslEnabled() ? $this->createWithSsl($options) : $this->createWithoutSsl($options);
    }

    private function createWithSsl(AmqpConnectionOptions $options): AbstractConnection
    {
        return new AMQPSSLConnection(
            $options->getHost(),
            $options->getPort(),
            $options->getUser(),
            $options->getPassword(),
            $options->getVhost(),
            [
                "verify_peer" => $options->isSslVerifyPeer(),
                "verify_peer_name" => $options->isSslVerifyPeerName()
            ],
            [
                "connection_timeout" => $options->getConnectionTimeout(),
                "read_write_timeout" => $options->getReadWriteTimeout(),
                "keepalive" => false,
                "heartbeat" => $options->getHeartbeat()
            ]
        );
    }

    private function createWithoutSsl(AmqpConnectionOptions $options): AbstractConnection
    {
        return new AMQPStreamConnection(
            $options->getHost(),
            $options->getPort(),
            $options->getUser(),
            $options->getPassword(),
            $options->getVhost(),
            false,
            'AMQPLAIN',
            null,
            'en_US',
            $options->getConnectionTimeout(),
            $options->getReadWriteTimeout(),
            null,
            $options->isKeepalive(),
            $options->getHeartbeat()
        );
    }
}