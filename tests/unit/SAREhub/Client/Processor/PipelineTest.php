<?php

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\Pipeline;
use SAREhub\Client\Processor\Processor;

class PipelineTest extends TestCase {
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $processorMock1;
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $processorMock2;
	
	/**
	 * @var Pipeline
	 */
	private $pipeline;
	
	public function setUp() {
		$this->processorMock1 = $this->createMock(Processor::class);
		$this->processorMock2 = $this->createMock(Processor::class);
		
		$this->pipeline = new Pipeline();
	}
	
	public function testAdd() {
		$this->assertSame($this->pipeline, $this->pipeline->add($this->processorMock1));
		$this->assertEquals([$this->processorMock1], $this->pipeline->getProcessors());
	}
	
	public function testNextAdd() {
		$this->pipeline->add($this->processorMock1)->add($this->processorMock2);
		$this->assertEquals([$this->processorMock1, $this->processorMock2], $this->pipeline->getProcessors());
	}
	
	public function testAddAll() {
		$this->pipeline->addAll([$this->processorMock1, $this->processorMock2]);
		$this->assertEquals([$this->processorMock1, $this->processorMock2], $this->pipeline->getProcessors());
	}
	
	public function testClear() {
		$this->assertSame($this->pipeline, $this->pipeline->add($this->processorMock1)->clear());
		$this->assertEmpty($this->pipeline->getProcessors());
	}
	
	public function testProcess() {
		$this->pipeline->add($this->processorMock1)->add($this->processorMock2);
		$exchange = new BasicExchange();
		$orginalMessage = BasicMessage::withBody('start');
		$exchange->setIn($orginalMessage);
		
		$this->processorMock1->expects($this->once())
		  ->method('process')
		  ->with($this->callback(function ($exchange) {
			  return $exchange instanceof Exchange && $exchange->getIn()->getBody() === 'start';
		  }))
		  ->willReturnCallback(function () use ($exchange) {
			  $exchange->getOut()->setBody('afterProcess1');
		  });
		
		$this->processorMock2->expects($this->once())
		  ->method('process')
		  ->with($this->callback(function ($exchange) {
			  return $exchange instanceof Exchange && $exchange->getIn()->getBody() === 'afterProcess1';
		  }))
		  ->willReturnCallback(function () use ($exchange) {
			  $exchange->getOut()->setBody('afterProcess2');
		  });
		
		$this->pipeline->process($exchange);
		
		$this->assertSame($orginalMessage, $exchange->getIn());
		$this->assertEquals('afterProcess2', $exchange->getOut()->getBody());
	}
}
