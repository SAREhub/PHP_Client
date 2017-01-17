<?php

namespace SAREhub\Client\Processor;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use SAREhub\Client\DataFormat\DataFormat;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Exchange;

class MarshalProcessorTest extends TestCase {
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $dataFormat;
	
	/**
	 * @var MarshalProcessor
	 */
	private $processor;
	
	/**
	 * @var Exchange
	 */
	private $exchange;
	
	public function setUp() {
		$this->dataFormat = $this->createPartialMock(DataFormat::class, ['marshal', 'unmarshal', '__toString']);
		$this->processor = MarshalProcessor::withDataFormat($this->dataFormat);
		$this->exchange = BasicExchange::newInstance()->setIn(BasicMessage::newInstance());
	}
	
	public function testProcessThenDataFormatMarshal() {
		$this->dataFormat->expects($this->once())
		  ->method('marshal')
		  ->with($this->identicalTo($this->exchange));
		
		$this->processor->process($this->exchange);
	}
	
	public function testProcessThenExchangeOutBody() {
		$marshaled = 'marshaledData';
		$this->dataFormat->method('marshal')->willReturn($marshaled);
		$this->processor->process($this->exchange);
		$this->assertEquals($marshaled, $this->exchange->getOut()->getBody());
	}
	
	public function testProcessThenExchangeOutPreservedHeaders() {
		$this->dataFormat->method('marshal')->willReturn('marshaledData');
		$this->exchange->getIn()->setHeaders(['header1' => 1, 'header2' => 2]);
		$this->processor->process($this->exchange);
		$this->assertEquals($this->exchange->getIn()->getHeaders(), $this->exchange->getOut()->getHeaders());
	}
	
	public function testToString() {
		$this->dataFormat->method('__toString')->willReturn('format');
		$this->assertEquals('Marshal[format]', (string)$this->processor);
	}
	
}
