<?php

namespace SAREhub\Client\Event;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\User\UserEvent;

class EventSerializationServiceTest extends TestCase {
	
	
	public function testRegisterSerializer() {
		$serializationService = new EventSerializationService();
		
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
		
		$serializationService = new EventSerializationService();
		$serializationService->registerSerializer("testEvent", $serializerMock);
		$this->assertEquals(json_encode($eventData), $serializationService->serialize($eventMock));
	}
	
	/**
	 * @expectedException \SAREhub\Client\Event\EventSerializeException
	 */
	public function testSerializeWithUnregisteredSerializer() {
		$eventMock = $this->getMockBuilder(UserEvent::class)->disableOriginalConstructor()->getMock();
		$eventMock->method('getEventType')->willReturn('testEvent');
		$serializationService = new EventSerializationService();
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
		
		$serializationService = new EventSerializationService();
		$serializationService->registerSerializer("testEvent", $serializerMock);
		$serializationService->serialize($eventMock);
	}
	
	public function testRegisterDeserializer() {
		$serializationService = new EventSerializationService();
		
		$deserializerMock = $this->getCallbackMock();
		$serializationService->registerDeserializer("testEvent", $deserializerMock);
		$this->assertTrue($serializationService->hasDeserializer('testEvent'));
		$this->assertSame($deserializerMock, $serializationService->getDeserializer('testEvent'));
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
		
		$serializationService = new EventSerializationService();
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
		
		$serializationService = new EventSerializationService();
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
		
		$serializationService = new EventSerializationService();
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
		
		$serializationService = new EventSerializationService();
		$serializationService->registerDeserializer("testEvent", $deserializerMock);
		$serializationService->deserialize(json_encode($eventData));
	}
}
