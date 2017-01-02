<?php

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Processor\TransformProcessor;

class TransformProcessorTest extends TestCase {
	
	private $transformerMock;
	private $processor;
	
	protected function setUp() {
		$this->transformerMock = $this->getMockBuilder(stdClass::class)->setMethods(['__invoke'])->getMock();
		$this->processor = TransformProcessor::transform($this->transformerMock);
	}
	
	public function testProcess() {
		$exchange = new BasicExchange();
		$this->transformerMock->expects($this->once())
		  ->method('__invoke')
		  ->with($exchange);
		$this->processor->process($exchange);
	}
	
	public function testToString() {
		$this->processor->setId('t_id');
		$this->assertEquals('Transform[t_id]', (string)$this->processor);
	}
}
