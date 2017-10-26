<?php

namespace SAREhub\Client\Processor;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
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

    /**
     * @var Exchange
     */
    private $exchange;

    public function setUp()
    {
        $this->logger = \Mockery::mock(LoggerInterface::class);
        $this->processor = new LogProcessor($this->logger);
        $this->exchange = $this->createExchange();
    }

    public function testLogExchangeWhenLogLevelIsFoundAndIdIsNull()
    {
        $logLevel = "error";
        $this->processor->setLogLevel($logLevel);
        $this->logger->shouldReceive("log")->with($logLevel, "logProcessor output[id: null]", [$this->exchange])->once();
        $this->processor->process($this->exchange);
    }

    public function testLogExchangeWhenLogLevelIsFoundAndIdIsSet()
    {
        $logLevel = "error";
        $this->processor->setLogLevel($logLevel);
        $this->processor->setId("id");
        $this->logger->shouldReceive("log")->with($logLevel, "logProcessor output[id: id]", [$this->exchange])->once();
        $this->processor->process($this->exchange);
    }

    public function testLogExchangeWhenLogLevelIsInvalid()
    {
        $logLevel = "aesf";
        $this->processor->setLogLevel($logLevel);
        $this->logger->allows("log")->once()->andThrow(\InvalidArgumentException::class);
        $this->expectException(\InvalidArgumentException::class);
        $this->processor->process($this->exchange);
    }

    private function createExchange(): Exchange
    {
        return BasicExchange::newInstance()->setIn(BasicMessage::newInstance()->setBody("test"));
    }
}
