<?php

namespace SAREhub\Client\Processor;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\Exchange;

class SimpleFilterProcessorTest extends TestCase {
	
	/**
	 * @var SimpleFilterProcessor
	 */
	private $processor;
	
	/**
	 * @var callable|PHPUnit_Framework_MockObject_MockObject
	 */
	private $predicate;
	/**
	 * @var Processor | PHPUnit_Framework_MockObject_MockObject
	 */
	private $onPassProcessor;
	
	/**
	 * @var Exchange
	 */
	private $exchange;
	
	protected function setUp() {
		$this->exchange = BasicExchange::newInstance();
		$this->predicate = $this->createPartialMock(\stdClass::class, ['__invoke']);
		$this->onPassProcessor = $this->createMock(Processor::class);
		$this->processor = SimpleFilterProcessor::newInstance()
		  ->withPredicate($this->predicate)
		  ->withOnPass($this->onPassProcessor);
	}
	
	public function testProcessThenPredicateCall() {
		$this->predicate->expects($this->once())->method('__invoke')->with($this->exchange);
		$this->processor->process($this->exchange);
	}
	
	public function testProcessWhenPassedThenContinueProcess() {
		$this->predicate->method('__invoke')->willReturn(true);
		$this->onPassProcessor->expects($this->once())->method('process')->with($this->exchange);
		$this->processor->process($this->exchange);
	}
	
	public function testProcessWhenNotPassThenStopProcess() {
		$this->onPassProcessor->expects($this->never())->method('process');
		$this->processor->process($this->exchange);
	}
}
