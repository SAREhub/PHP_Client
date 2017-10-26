<?php

namespace SAREhub\Client\Processor;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Exchange;

class LogProcessorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Mock | LoggerInterface
     */
    private $logger;

    /**
     * @var LogProcessor
     */
    private $processor;

    public function setUp()
    {
        $this->logger = \Mockery::mock(LoggerInterface::class);
        $this->processor = new LogProcessor();
        $this->processor->setLogger($this->logger);
    }

    public function testLogExchange()
    {
        $exchange = $this->createExchange();
        $this->logger->shouldReceive("info")->with($exchange)->once();
        $this->processor->process($exchange);
    }

    private function createExchange(): Exchange {
        return BasicExchange::newInstance()->setIn(BasicMessage::newInstance()->setBody("test"));
    }
}
