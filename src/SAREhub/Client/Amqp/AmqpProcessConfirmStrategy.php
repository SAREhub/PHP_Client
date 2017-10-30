<?php

namespace SAREhub\Client\Amqp;

use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Message\Message;

interface AmqpProcessConfirmStrategy
{
    public function confirm(AmqpChannelWrapper $channel, Message $orginalIn, Exchange $exchange);
}