<?php

namespace SAREhub\Client\Event;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\DataFormat\DataFormat;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\User\User;

class RawEventDataFormatTest extends TestCase {
	
	/**
	 * @var DataFormat
	 */
	private $dataFormat;
	
	protected function setUp() {
		$this->dataFormat = new RawEventDataFormat();
	}
	
	public function testMarshalWhenEmptyUserThenReturn() {
		$event = BasicEvent::newInstanceOf('test')
		  ->withTime(100)
		  ->withProperty('prop1', 1);
		$marshaled = $this->dataFormat->marshal($this->createExchange($event));
		$this->assertEquals([
		  'type' => $event->getType(),
		  'time' => $event->getTime(),
		  'params' => $event->getProperties()
		], $marshaled);
	}
	
	public function testMarshalWhenNotLegacyThenReturn() {
		$event = BasicEvent::newInstanceOf('test')
		  ->withTime(100)
		  ->withUser(new User(['k1' => 1]))
		  ->withProperty('prop1', 1);
		$marshaled = $this->dataFormat->marshal($this->createExchange($event));
		$this->assertEquals([
		  'type' => $event->getType(),
		  'time' => $event->getTime(),
		  'user' => $event->getUser()->getKeys(),
		  'params' => $event->getProperties()
		], $marshaled);
	}
	
	public function testMarshalWhenLegacyThenReturn() {
		$event = BasicEvent::newInstanceOf('test')
		  ->withTime(100)
		  ->withUser(new User(['k1' => 1]))
		  ->withProperty('prop1', 1)
		  ->withProperty('extra', ['e1' => 2]);
		
		$marshaled = $this->dataFormat->marshal($this->createExchange($event));
		$this->assertEquals([
		  'type' => $event->getType(),
		  'time' => $event->getTime(),
		  'user' => $event->getUser()->getKeys(),
		  'params' => $event->getProperties(),
		  'extra' => ['e1' => 2]
		], $marshaled);
	}
	
	public function testUnmarshalThenReturn() {
		$eventData = [
		  'type' => 'test',
		  'time' => 100,
		  'user' => [
			'k1' => 1
		  ],
		  'params' => [
			'prop1' => 'p1'
		  ]
		];
		
		/** @var Event $event */
		$event = $this->dataFormat->unmarshal($this->createExchange($eventData));
		$this->assertEquals($eventData['type'], $event->getType());
		$this->assertEquals($eventData['time'], $event->getTime());
		$this->assertEquals($eventData['user'], $event->getUser()->getKeys());
		$this->assertEquals($eventData['params'], $event->getProperties());
	}
	
	private function createExchange($inData) {
		return BasicExchange::newInstance()
		  ->setIn(BasicMessage::newInstance()
			->setBody($inData));
	}
}
