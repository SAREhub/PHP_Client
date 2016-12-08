<?php

namespace unit\SAREhub\Client\Event;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\BasicEvent;
use SAREhub\Client\Event\EventPropertyNotFoundException;

class BasicEventTest extends TestCase {
	
	public function testGetAttributeWhenExistsThenReturnValue() {
		$event = BasicEvent::newInstanceOf('type')->withProperty('test', 'value');
		$this->assertEquals('value', $event->getProperty('test'));
	}
	
	public function testGetAttributeWhenNotExistsThenThrowException() {
		$event = BasicEvent::newInstanceOf('type');
		$this->expectException(EventPropertyNotFoundException::class);
		$event->getProperty('test');
	}
	
	public function testHasAttributeWhenExistsThenReturnTrue() {
		$event = BasicEvent::newInstanceOf('type')->withProperty('test', 'value');
		$this->assertTrue($event->hasProperty('test'));
	}
	
	public function testHasAttributeWhenNotExistsThenReturnFalse() {
		$event = BasicEvent::newInstanceOf('type');
		$this->assertFalse($event->hasProperty('test'));
	}
}
