<?php

namespace SAREhub\Client\Amqp;

use GuzzleHttp\Promise\Promise;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use SAREhub\Client\Event\BasicEventEnvelope;
use SAREhub\Client\Event\Event;
use SAREhub\Client\Event\EventDeserializationService;

class AmqpEventEnvelopeFactoryTest extends TestCase {
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $eventDeserializationServiceMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $eventMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $eventEnvelopePropertiesFactoryMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $eventEnvelopePropertiesMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $eventProcessPromiseFactory;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $processPromiseMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $amqpMessageMock;
	
	/** @var AmqpEventEnvelopeFactory */
	private $factory;
	
	protected function setUp() {
		$this->eventDeserializationServiceMock = $this->createMock(EventDeserializationService::class);
		$this->eventMock = $this->createMock(Event::class);
		
		$this->eventEnvelopePropertiesFactoryMock = $this->createMock(AmqpEventEnvelopePropertiesFactory::class);
		$this->eventEnvelopePropertiesMock = $this->createMock(AmqpEventEnvelopeProperties::class);
		
		$this->eventProcessPromiseFactory = $this->createMock(AmqpDeliveredEventProcessPromiseFactory::class);
		$this->processPromiseMock = $this->createMock(Promise::class);
		
		$this->amqpMessageMock = $this->createMock(AMQPMessage::class);
		
		$this->factory = AmqpEventEnvelopeFactory::factory()
		  ->eventDeserializationService($this->eventDeserializationServiceMock)
		  ->propertiesFactory($this->eventEnvelopePropertiesFactoryMock)
		  ->processPromiseFactory($this->eventProcessPromiseFactory);
	}
	
	
	public function testCreateFromDeliveredMessage() {
		$this->amqpMessageMock->expects($this->once())->method('getBody')->willReturn('body');
		$this->eventDeserializationServiceMock->expects($this->once())
		  ->method('deserialize')
		  ->with('body')
		  ->willReturn($this->eventMock);
		
		$this->eventEnvelopePropertiesFactoryMock->expects($this->once())
		  ->method('createFromMessage')
		  ->with($this->identicalTo($this->amqpMessageMock))
		  ->willReturn($this->eventEnvelopePropertiesMock);
		
		$this->eventProcessPromiseFactory->expects($this->once())
		  ->method('create')
		  ->with($this->isInstanceOf(BasicEventEnvelope::class))
		  ->willReturn($this->processPromiseMock);
		
		$eventEnvelope = $this->factory->createFromDeliveredMessage($this->amqpMessageMock);
		
		$this->assertSame($this->eventMock, $eventEnvelope->getEvent());
		$this->assertSame($this->eventEnvelopePropertiesMock, $eventEnvelope->getProperties());
		$this->assertSame($this->processPromiseMock, $eventEnvelope->getProcessPromise());
	}
	
}
