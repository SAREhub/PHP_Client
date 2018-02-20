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
        $transformer = $this->createCallable();
        $this->assertSame($transformer, Processors::transform($transformer)->getTransformer());
    }

    public function testPipeline()
    {
        $this->assertInstanceOf(Pipeline::class, Processors::pipeline());
    }

    public function testMulticast()
    {
        $this->assertInstanceOf(MulticastProcessor::class, Processors::multicast());
    }

    public function testRouter()
    {
        $routingFunction = $this->createCallable();
        $this->assertSame($routingFunction, Processors::router($routingFunction)->getRoutingFunction());
    }

    public function testFilter()
    {
        $predicate = $this->createCallable();
        $this->assertSame($predicate, Processors::filter($predicate)->getPredicate());
    }

    private function createCallable(): callable
    {
        return function (Exchange $exchange) {
        };
    }

}
