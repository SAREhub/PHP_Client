<?php

namespace SAREhub\Client\Processor;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class ExchangeCatcherProcessorTest extends TestCase
{

    /**
     * @var ExchangeCatcherProcessor
     */
    private $catcher;

    protected function setUp()
    {
        $this->catcher = new ExchangeCatcherProcessor();
    }

    public function testProcess()
    {
        $exchange = BasicExchange::withIn(BasicMessage::withBody("test"));

        $this->catcher->process($exchange);
        
        $history = $this->catcher->getCaughtHistory();
        $this->assertNotSame($exchange, $history[0]);
        $this->assertEquals([$exchange], $history);
    }

    public function testGetCaughtInMessages()
    {
        $exchange = BasicExchange::withIn(BasicMessage::withBody("test"));

        $this->catcher->process($exchange);

        $caughtInMessages = $this->catcher->getCaughtInMessages();
        $this->assertNotSame($exchange->getIn(), $caughtInMessages[0]);
        $this->assertEquals($exchange->getIn(), $caughtInMessages[0]);
    }
}
