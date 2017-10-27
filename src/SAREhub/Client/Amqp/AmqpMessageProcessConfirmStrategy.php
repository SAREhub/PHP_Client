<?php

namespace SAREhub\Client\Amqp;

use SAREhub\Client\Message\Exchange;

interface AmqpMessageProcessConfirmStrategy
{
    public function confirm(Exchange $orginal, Exchange $afterProcess);
}