<?php

namespace SAREhub\Client\Processor;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use SAREhub\Client\Message\Exchange;

class ProcessorsTest extends TestCase
{
    public function testBlackhole()
    {
        $this->assertInstanceOf(NullProcessor::class, Processors::blackhole());
    }

    public function testLogProcessor()
    {
        $logger = new NullLogger();
        $this->assertSame($logger, Processors::log($logger)->getLogger());
    }

    public function testTransform()
    {
        $transformer = function (Exchange $exchange) {
        };
        $this->assertSame($transformer, Processors::transform($transformer)->getTransformer());
    }

    public function testRouter()
    {
        $routingFunction = function (Exchange $exchange) { };
        $this->assertSame($routingFunction, Processors::router($routingFunction)->getRoutingFunction());
    }

}
