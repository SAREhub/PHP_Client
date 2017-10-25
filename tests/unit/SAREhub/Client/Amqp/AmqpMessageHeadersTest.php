<?php

namespace SAREhub\Client\Amqp;

use PHPUnit\Framework\TestCase;

class AmqpMessageHeadersTest extends TestCase
{

    public function testGetMessagePropertyName()
    {
        $name = AmqpMessageHeaders::getMessagePropertyName(AmqpMessageHeaders::CONSUMER_TAG);
        $this->assertEquals('consumer_tag', $name);
    }
}
