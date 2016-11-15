<?php

namespace SAREhub\Client\Event;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\User\UserEvent;

class JsonEventSerializationServiceTest extends TestCase {
	
	private $event;
	private $serializer;
	
	/**
	 * @var JsonEventSerializationService
	 */
	private $service;
	
	protected function setUp() {
		$this->event = $this->createMock(BasicEvent::class);
		$this->event->method('getEventType')->willReturn('testEvent');
		
		$this->serializer = $this->createPartialMock(\stdClass::class, ['__invoke']);
		$this->service = new JsonEventSerializationService();
	}
	
	public function testRegisterSerializerThenHasSerializer() {
		$this->service->registerSerializer("testEvent", $this->serializer);
		$this->assertTrue($this->service->hasSerializer('testEvent'));
	}
	
	public function testRegisterSerializerThenGetSerilizer() {
		$this->service->registerSerializer("testEvent", $this->serializer);
		$this->assertSame($this->serializer, $this->service->getSerializer('testEvent'));
	}
	
	public function testSerializeThenReturnEventDataJson() {
		$eventData = [
		  'type' => 'testEvent',
		  'user' => [
			'email' => 'example@example.com'
		  ],
		  'params' => [
			'param1' => 1
		  ]
		];
		
		$this->serializer->expects($this->once())
		  ->method('__invoke')
		  ->with(self::identicalTo($this->event))
		  ->willReturn($eventData);
		
		$this->service->registerSerializer("testEvent", $this->serializer);
		$this->assertEquals(json_encode($eventData), $this->service->serialize($this->event));
	}
	
	public function testSerializeWhenNotRegisteredSerializerThenThrowException() {
		$this->expectException(EventSerializeException::class);
		$this->service->serialize($this->event);
	}
	
	public function testSerializeWhenSerializerReturnNullThenThrowException() {
		$this->serializer->method('__invoke')->willReturn(null);
		$this->service->registerSerializer("testEvent", $this->serializer);
		$this->expectException(EventSerializeException::class);
		$this->service->serialize($this->event);
	}
	
}
