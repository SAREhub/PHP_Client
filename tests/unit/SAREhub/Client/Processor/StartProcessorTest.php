<?php

namespace SAREhub\Client\Processor;

use PHPUnit\Framework\TestCase;

class StartProcessorTest extends TestCase
{

    public function testProcess()
    {
        $processor = new ExchangeCatcherProcessor();
        $startProcessor = StartProcessor::create($processor);

        $body = "test";
        $headers = ["test" => "test"];
        $current = $startProcessor->process($body, $headers);

        $history = $processor->getCaughtHistory();
        $this->assertCount(1, $history);
        $this->assertEquals($history[0], $current);
        $this->assertEquals($body, $current->getInBody());
        $this->assertEquals($headers, $current->getIn()->getHeaders());
    }
}
