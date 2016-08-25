<?php

namespace SAREhub\Client\Event;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\User\UserEvent;

class JsonEventSerializationServiceTest extends TestCase {
	
	public function testRegisterSerializer() {
		$serializationService = new JsonEventSerializationService();
		
		$serializerMock = $this->getCallbackMock();
		$serializationService->registerSerializer("testEvent", $serializerMock);
		$this->assertTrue($serializationService->hasSerializer('testEvent'));
		$this->assertSame($serializerMock, $serializationService->getSerializer('testEvent'));
	}
	
	private function getCallbackMock() {
		return $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
	}
	
	public function testSerialize() {
		$eventMock = $this->getMockBuilder(UserEvent::class)->disableOriginalConstructor()->getMock();
		$eventMock->method('getEventType')->willReturn('testEvent');
		
		$eventData = [
		  'type' => 'testEvent',
		  'user' => [
			'email' => 'example@example.com'
		  ],
		  'params' => [
			'param1' => 1
		  ]
		];
		
		$serializerMock = $this->getCallbackMock();
		$serializerMock->expects($this->once())->method('__invoke')->with($eventMock)->willReturn($eventData);
		
		$serializationService = new JsonEventSerializationService();
		$serializationService->registerSerializer("testEvent", $serializerMock);
		$this->assertEquals(json_encode($eventData), $serializationService->serialize($eventMock));
	}
	
	/**
	 * @expectedException \SAREhub\Client\Event\EventSerializeException
	 */
	public function testSerializeWithUnregisteredSerializer() {
		$eventMock = $this->getMockBuilder(UserEvent::class)->disableOriginalConstructor()->getMock();
		$eventMock->method('getEventType')->willReturn('testEvent');
		$serializationService = new JsonEventSerializationService();
		$serializationService->serialize($eventMock);
	}
	
	/**
	 * @expectedException \SAREhub\Client\Event\EventSerializeException
	 */
	public function testSerializeWithSerializerWhoReturnEmptyValue() {
		$eventMock = $this->getMockBuilder(UserEvent::class)->disableOriginalConstructor()->getMock();
		$eventMock->method('getEventType')->willReturn('testEvent');
		
		$eventData = [
		  'type' => 'testEvent',
		  'user' => [
			'email' => 'example@example.com'
		  ],
		  'params' => [
			'param1' => 1
		  ]
		];
		
		$serializerMock = $this->getCallbackMock();
		$serializerMock->expects($this->once())->method('__invoke')->with($eventMock)->willReturn(null);
		
		$serializationService = new JsonEventSerializationService();
		$serializationService->registerSerializer("testEvent", $serializerMock);
		$serializationService->serialize($eventMock);
	}
	
	
}
