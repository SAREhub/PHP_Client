<?php


namespace SAREhub\Client\Amqp;


interface AmqpConnectionOptionsProvider
{
    public function get(): AmqpConnectionOptions;
}