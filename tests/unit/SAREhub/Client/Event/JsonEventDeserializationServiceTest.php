<?php

namespace SAREhub\Client\Event;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\User\UserEvent;

class JsonEventDeserializationServiceTest extends TestCase {
	
	private $event;
	private $eventData;
	private $deserializer;
	
	/**
	 * @var EventDeserializationService
	 */
	private $service;
	
	protected function setUp() {
		$this->event = $this->createMock(BasicEvent::class);
		$this->event->method('getEventType')->willReturn('testEvent');
		
		$this->eventData = [
		  'type' => 'testEvent',
		  'user' => [
			'email' => 'example@example.com'
		  ],
		  'params' => [
			'param1' => 1
		  ]
		];
		
		$this->deserializer = $this->createPartialMock(\stdClass::class, ['__invoke']);
		
		$this->service = new JsonEventDeserializationService();
	}
	
	public function testRegisterDeserializer() {
		$this->service->registerDeserializer("testEvent", $this->deserializer);
		$this->assertTrue($this->service->hasDeserializer('testEvent'));
		$this->assertSame($this->deserializer, $this->service->getDeserializer('testEvent'));
	}
	
	public function testDeserialize() {
		
		$this->deserializer->expects($this->once())
		  ->method('__invoke')
		  ->with($this->eventData)
		  ->willReturn($this->event);
		
		$this->service->registerDeserializer("testEvent", $this->deserializer);
		$this->assertSame($this->event, $this->service->deserialize(json_encode($this->eventData)));
	}
	
	public function testDeserializeWhenInvalidJsonThenNotCallDeserializer() {
		$this->service->registerDeserializer("testEvent", $this->deserializer);
		$this->deserializer->expects($this->never())->method('__invoke');
		$this->expectException(EventDeserializeException::class);
		$this->service->deserialize(json_encode($this->eventData)."invalidString");
	}
	
	public function testDeserializeWhenInvalidJsonThenThrowException() {
		$this->service->registerDeserializer("testEvent", $this->deserializer);
		$this->expectException(EventDeserializeException::class);
		$this->service->deserialize(json_encode($this->eventData)."invalidString");
	}
	
	public function testDeserializeWhenUnregisteredDeserializerThenThrowException() {
		$this->expectException(EventDeserializeException::class);
		$this->service->deserialize(json_encode($this->eventData));
	}
	
	public function testDeserializeWithDeserializerWhoReturnNonEventObjectValue() {
		$this->service->registerDeserializer("testEvent", $this->deserializer);
		$this->expectException(EventDeserializeException::class);
		$this->service->deserialize(json_encode($this->eventData));
	}
	
}
