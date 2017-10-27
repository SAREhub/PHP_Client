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

    public function testProcessWhenIdIsNull()
    {
        $logLevel = "error";
        $this->processor->setLogLevel($logLevel);
        $this->logger->expects("log")->with($logLevel, (string)$this->processor, ["exchange" => $this->exchange])->once();
        $this->processor->process($this->exchange);
    }

    public function testProcessWhenIdIsSet()
    {
        $logLevel = "error";
        $this->processor->setLogLevel($logLevel);
        $this->processor->setId("id");
        $this->logger->expects("log")->with($logLevel, (string)$this->processor, ["exchange" => $this->exchange])->once();
        $this->processor->process($this->exchange);
    }

    public function testSetLogLevelWhenIsInvalid()
    {
        $logLevel = "aesf";
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid LogLevel: $logLevel");
        $this->processor->setLogLevel($logLevel);
    }

    public function testSetLogLevelWhenIsValid()
    {
        $logLevel = "debug";
        $this->processor->setLogLevel($logLevel);
        $this->assertEquals($logLevel, $this->processor->getLogLevel());
    }

    public function testSetLogLevelWhenContentIsValidAndInvalidCases()
    {
        $logLevel = "dEbUg";
        $this->processor->setLogLevel($logLevel);
        $this->assertEquals($logLevel, $this->processor->getLogLevel());
    }

    private function createExchange(): Exchange
    {
        return BasicExchange::newInstance()->setIn(BasicMessage::newInstance()->setBody("test"));
    }
}
