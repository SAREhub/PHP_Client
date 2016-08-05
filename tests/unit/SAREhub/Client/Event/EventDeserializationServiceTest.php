<?php

namespace SAREhub\Client\Event;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\User\UserEvent;

class EventDeserializationServiceTest extends TestCase {
	
	public function testRegisterDeserializer() {
		$serializationService = new EventDeserializationService();
		
		$deserializerMock = $this->getCallbackMock();
		$serializationService->registerDeserializer("testEvent", $deserializerMock);
		$this->assertTrue($serializationService->hasDeserializer('testEvent'));
		$this->assertSame($deserializerMock, $serializationService->getDeserializer('testEvent'));
	}
	
	private function getCallbackMock() {
		return $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
	}
	
	public function testDeserialize() {
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
		
		$deserializerMock = $this->getCallbackMock();
		$deserializerMock->expects($this->once())->method('__invoke')->with($eventData)->willReturn($eventMock);
		
		$serializationService = new EventDeserializationService();
		$serializationService->registerDeserializer("testEvent", $deserializerMock);
		$this->assertSame($eventMock, $serializationService->deserialize(json_encode($eventData)));
	}
	
	/**
	 * @expectedException \SAREhub\Client\Event\EventDeserializeException
	 */
	public function testDeserializeInvalidJson() {
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
		
		$deserializerMock = $this->getCallbackMock();
		$deserializerMock->expects($this->never())->method('__invoke')->with($eventData)->willReturn($eventMock);
		
		$serializationService = new EventDeserializationService();
		$serializationService->registerDeserializer("testEvent", $deserializerMock);
		$this->assertSame($eventMock, $serializationService->deserialize(json_encode($eventData)."invalidString"));
	}
	
	/**
	 * @expectedException \SAREhub\Client\Event\EventDeserializeException
	 */
	public function testDeserializeWithUnregisteredDeserializer() {
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
		
		$serializationService = new EventDeserializationService();
		$serializationService->deserialize(json_encode($eventData));
	}
	
	/**
	 * @expectedException \SAREhub\Client\Event\EventDeserializeException
	 */
	public function testDeserializeWithDeserializerWhoReturnNonEventObjectValue() {
		$eventData = [
		  'type' => 'testEvent',
		  'user' => [
			'email' => 'example@example.com'
		  ],
		  'params' => [
			'param1' => 1
		  ]
		];
		
		$deserializerMock = $this->getCallbackMock();
		$deserializerMock->expects($this->once())->method('__invoke')->with($eventData)->willReturn(null);
		
		$serializationService = new EventDeserializationService();
		$serializationService->registerDeserializer("testEvent", $deserializerMock);
		$serializationService->deserialize(json_encode($eventData));
	}
	
}
