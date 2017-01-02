<?php

namespace SAREhub\Client\Processor;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use SAREhub\Client\DataFormat\DataFormat;
use SAREhub\Client\Message\BasicExchange;

class MarshalProcessorTest extends TestCase {
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $dataFormatMock;
	
	/**
	 * @var MarshalProcessor
	 */
	private $processor;
	
	private $exchange;
	
	public function setUp() {
		$this->dataFormatMock = $this->createPartialMock(DataFormat::class, ['marshal', 'unmarshal', '__toString']);
		$this->processor = MarshalProcessor::withDataFormat($this->dataFormatMock);
		$this->exchange = new BasicExchange();
	}
	
	
	public function testProcess() {
		$this->dataFormatMock->expects($this->once())
		  ->method('marshal')
		  ->with($this->identicalTo($this->exchange));
		
		$this->processor->process($this->exchange);
	}
	
	public function testToString() {
		$this->dataFormatMock->method('__toString')->willReturn('format');
		$this->assertEquals('Marshal[format]', (string)$this->processor);
	}
	
}
