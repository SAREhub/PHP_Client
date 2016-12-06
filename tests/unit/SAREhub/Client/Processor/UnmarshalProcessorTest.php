<?php

namespace SAREhub\Client\Processor;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\DataFormat\DataFormat;
use SAREhub\Client\Message\BasicExchange;

class UnmarshalProcessorTest extends TestCase {
	
	private $dataFormatMock;
	
	/**
	 * @var UnmarshalProcessor
	 */
	private $processor;
	
	private $exchange;
	
	public function setUp() {
		$this->dataFormatMock = $this->createMock(DataFormat::class);
		$this->processor = UnmarshalProcessor::withDataFormat($this->dataFormatMock);
		$this->exchange = new BasicExchange();
	}
	
	public function testProcess() {
		$this->dataFormatMock->expects($this->once())
		  ->method('unmarshal')
		  ->with($this->identicalTo($this->exchange));
		$this->processor->process($this->exchange);
	}
	
}
