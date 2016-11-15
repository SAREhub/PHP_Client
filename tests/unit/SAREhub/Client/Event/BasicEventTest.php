<?php

namespace unit\SAREhub\Client\Event;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\BasicEvent;
use SAREhub\Client\Event\EventAttributeNotFoundException;

class BasicEventTest extends TestCase {
	
	public function testGetAttributeWhenExistsThenReturnValue() {
		$event = BasicEvent::newInstanceOf('type')->withAttribute('test', 'value');
		$this->assertEquals('value', $event->getAttribute('test'));
	}
	
	public function testGetAttributeWhenNotExistsThenThrowException() {
		$event = BasicEvent::newInstanceOf('type');
		$this->expectException(EventAttributeNotFoundException::class);
		$event->getAttribute('test');
	}
	
	public function testHasAttributeWhenExistsThenReturnTrue() {
		$event = BasicEvent::newInstanceOf('type')->withAttribute('test', 'value');
		$this->assertTrue($event->hasAttribute('test'));
	}
	
	public function testHasAttributeWhenNotExistsThenReturnFalse() {
		$event = BasicEvent::newInstanceOf('type');
		$this->assertFalse($event->hasAttribute('test'));
	}
}
