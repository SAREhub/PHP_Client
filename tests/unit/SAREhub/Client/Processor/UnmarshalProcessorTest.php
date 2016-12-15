<?php

namespace SAREhub\Client\Processor;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\DataFormat\DataFormat;
use SAREhub\Client\Message\BasicExchange;

class UnmarshalProcessorTest extends TestCase {
	
	private $dataFormat;
	
	/**
	 * @var UnmarshalProcessor
	 */
	private $processor;
	
	private $exchange;
	
	public function setUp() {
		$this->dataFormat = $this->createPartialMock(DataFormat::class, ['marshal', 'unmarshal', '__toString']);
		$this->processor = UnmarshalProcessor::withDataFormat($this->dataFormat);
		$this->exchange = new BasicExchange();
	}
	
	public function testProcess() {
		$this->dataFormat->expects($this->once())
		  ->method('unmarshal')
		  ->with($this->identicalTo($this->exchange));
		$this->processor->process($this->exchange);
	}
	
	public function testToString() {
		$this->dataFormat->method('__toString')->willReturn('format');
		$this->assertEquals('Unmarshal[format]', (string)$this->processor);
	}
	
}
