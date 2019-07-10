<?php


namespace SAREhub\Client\Amqp\Schema;


use Throwable;

class AmqpSchemaException extends \RuntimeException
{
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}