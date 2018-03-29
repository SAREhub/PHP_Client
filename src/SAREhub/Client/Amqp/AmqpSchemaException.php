<?php


namespace SAREhub\Client\Amqp;


use Throwable;

class AmqpSchemaException extends \Exception
{
    public function __construct(string $message = "", Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}