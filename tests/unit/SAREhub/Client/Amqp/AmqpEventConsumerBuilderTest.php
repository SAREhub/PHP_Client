<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use SAREhub\Client\Event\EventEnvelope;
use SAREhub\Client\Event\EventStreamSink;

class AmqpEventConsumerBuilderTest extends TestCase {
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $sourceMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $sinkMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $eventEnvelopeMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $eventEnvelopeFactoryMock;
	
	public function setUp() {
		$this->sourceMock = $this->createMock(BasicAmqpEventStreamSource::class);
		$this->sinkMock = $this->createMock(EventStreamSink::class);
		$this->sourceMock->method('getSink')->willReturn($this->sinkMock);
		
		$this->eventEnvelopeFactoryMock = $this->createMock(AmqpEventEnvelopeFactory::class);
		$this->eventEnvelopeMock = $this->createMock(EventEnvelope::class);
	}
	
	public function testBuild() {
		$builder = new AmqpEventConsumerBuilder();
		$builder->source($this->sourceMock);
		$builder->eventEnvelopeFactory($this->eventEnvelopeFactoryMock);
		$this->assertInstanceOf(\Closure::class, $builder->build());
	}
	
	public function testConsume() {
		$builder = new AmqpEventConsumerBuilder();
		$builder->source($this->sourceMock);
		$builder->eventEnvelopeFactory($this->eventEnvelopeFactoryMock);
		$this->assertInstanceOf(\Closure::class, $builder->build());
		
		$consumer = $builder->build();
		
		$amqpMessageMock = $this->createMock(AMQPMessage::class);
		$this->eventEnvelopeFactoryMock->expects($this->once())
		  ->method('createFromDeliveredMessage')
		  ->with($this->identicalTo($amqpMessageMock))
		  ->willReturn($this->eventEnvelopeMock);
		
		$this->sinkMock->expects($this->once())
		  ->method('write')
		  ->with($this->identicalTo($this->eventEnvelopeMock));
		
		$consumer($amqpMessageMock);
	}
	
	/**
	 * @expectedException \Respect\Validation\Exceptions\ValidationException
	 */
	public function testBuildWithoutSource() {
		$builder = new AmqpEventConsumerBuilder();
		$builder->eventEnvelopeFactory($this->eventEnvelopeFactoryMock);
		$this->assertInstanceOf(\Closure::class, $builder->build());
	}
	
	/**
	 * @expectedException \Respect\Validation\Exceptions\ValidationException
	 */
	public function testBuildWithoutEventEnvelopeFactory() {
		$builder = new AmqpEventConsumerBuilder();
		$builder->source($this->sourceMock);
		$this->assertInstanceOf(\Closure::class, $builder->build());
	}
	
}
