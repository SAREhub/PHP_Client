<?php

namespace SAREhub\Client\Event;

use GuzzleHttp\Promise\Promise;
use PHPUnit\Framework\TestCase;

class BasicEventEnvelopeTest extends TestCase {
	
	public function testMarkAsProcessed() {
		$event = $this->createMock(Event::class);
		$eventEnvelope = new BasicEventEnvelope($event);
		
		$processPromise = $this->createMock(Promise::class);
		$eventEnvelope->setProcessPromise($processPromise);
		$processPromise->expects($this->once())->method('resolve')->with($this->identicalTo($eventEnvelope));
		
		$eventEnvelope->markAsProcessed();
	}
	
	public function testMarkAsCancelled() {
		$event = $this->createMock(Event::class);
		$eventEnvelope = new BasicEventEnvelope($event);
		
		$processPromise = $this->createMock(Promise::class);
		$eventEnvelope->setProcessPromise($processPromise);
		$processPromise->expects($this->once())->method('cancel');
		
		$eventEnvelope->markAsCancelled();
	}
	
	public function testMarkAsProcessedExceptionally() {
		$event = $this->createMock(Event::class);
		$eventEnvelope = new BasicEventEnvelope($event);
		
		$processPromise = $this->createMock(Promise::class);
		$eventEnvelope->setProcessPromise($processPromise);
		$e = new \Exception("test");
		$processPromise->expects($this->once())->method('reject')->with($this->identicalTo($e));
		
		$eventEnvelope->markAsProcessedExceptionally($e);
	}
}
