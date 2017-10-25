<?php

namespace SAREhub\Client\Processor;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class HeaderAppenderProcessorTest extends TestCase
{

    public function testProcessThenMessageHeadersAppended()
    {
        $message = BasicMessage::newInstance()->setHeader('a', 1);
        $startHeaders = $message->getHeaders();

        $processor = HeaderAppenderProcessor::newInstance()
            ->withHeaders(['b' => 2, 'c' => 3]);
        $processor->process(BasicExchange::newInstance()->setIn($message));
        $this->assertEquals($startHeaders + $processor->getHeaders(), $message->getHeaders());
    }

    public function testToString()
    {
        $processor = HeaderAppenderProcessor::newInstance()
            ->withHeaders(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->assertEquals('HeaderAppender[a=1, b=2, c=3]', (string)$processor);
    }
}
