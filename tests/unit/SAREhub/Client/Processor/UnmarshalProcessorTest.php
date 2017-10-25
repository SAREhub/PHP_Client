<?php

namespace SAREhub\Client\Processor;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\DataFormat\DataFormat;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Exchange;

class UnmarshalProcessorTest extends TestCase
{

    private $dataFormat;

    /**
     * @var UnmarshalProcessor
     */
    private $processor;

    /**
     * @var Exchange
     */
    private $exchange;

    public function setUp()
    {
        $this->dataFormat = $this->createPartialMock(DataFormat::class, ['marshal', 'unmarshal', '__toString']);
        $this->processor = UnmarshalProcessor::withDataFormat($this->dataFormat);
        $this->exchange = BasicExchange::newInstance()->setIn(BasicMessage::newInstance());
    }

    public function testProcessDataFormatUnmarshal()
    {
        $this->dataFormat->expects($this->once())
            ->method('unmarshal')
            ->with($this->identicalTo($this->exchange));
        $this->processor->process($this->exchange);
    }

    public function testProcessThenExchangeOutBody()
    {
        $unmarshaled = 'unmarshaled_data';
        $this->dataFormat->method('unmarshal')->willReturn($unmarshaled);
        $this->processor->process($this->exchange);
        $this->assertEquals($unmarshaled, $this->exchange->getOut()->getBody());
    }

    public function testProcessThenExchangeOutPreservedHeaders()
    {
        $this->dataFormat->method('unmarshal')->willReturn('unmarshaled_data');
        $headers = [
            'header1' => 100,
            'header2' => 200
        ];

        $this->exchange->getIn()->setHeaders($headers);
        $this->processor->process($this->exchange);
        $this->assertEquals($headers, $this->exchange->getOut()->getHeaders());
    }

    public function testToString()
    {
        $this->dataFormat->method('__toString')->willReturn('format');
        $this->assertEquals('Unmarshal[format]', (string)$this->processor);
    }

}
