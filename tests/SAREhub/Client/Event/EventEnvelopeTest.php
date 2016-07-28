<?php

namespace SAREhub\Client\Event;

use PHPUnit\Framework\TestCase;

class EventEnvelopeTest extends TestCase {
	
	public function testProcessed() {
		$event = $this->createMock(Event::class);
		$eventEnvelope = new EventEnvelope($event);
		
		$processedCallbackMock = $this->getCallbackMock();
		$processedCallbackMock->expects($this->once())
		  ->method('__invoke')->with($this->identicalTo($eventEnvelope));
		$eventEnvelope->setProcessedCallback($processedCallbackMock);
		
		$cancelledCallbackMock = $this->getCallbackMock();
		$cancelledCallbackMock->expects($this->never())
		  ->method('__invoke')->with($this->identicalTo($eventEnvelope));
		$eventEnvelope->setCancelledCallback($cancelledCallbackMock);
		
		$eventEnvelope->processed();
		$this->assertTrue($eventEnvelope->isProcessed());
		$eventEnvelope->processed();
		$eventEnvelope->cancelled();
	}
	
	private function getCallbackMock() {
		return $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
	}
	
	public function testCancelled() {
		$event = $this->createMock(Event::class);
		$eventEnvelope = new EventEnvelope($event);
		
		$cancelledCallbackMock = $this->getCallbackMock();
		$cancelledCallbackMock->expects($this->once())
		  ->method('__invoke')->with($this->identicalTo($eventEnvelope));
		$eventEnvelope->setCancelledCallback($cancelledCallbackMock);
		
		$processedCallbackMock = $this->getCallbackMock();
		$processedCallbackMock->expects($this->never())
		  ->method('__invoke')->with($this->identicalTo($eventEnvelope));
		$eventEnvelope->setProcessedCallback($processedCallbackMock);
		
		$eventEnvelope->cancelled();
		$this->assertTrue($eventEnvelope->isCancelled());
		$eventEnvelope->cancelled();
		$eventEnvelope->processed();
	}
}
