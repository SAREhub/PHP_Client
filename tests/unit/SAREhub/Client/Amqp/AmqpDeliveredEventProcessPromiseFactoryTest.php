<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use SAREhub\Client\Event\BasicEventEnvelope;

class AmqpDeliveredEventProcessPromiseFactoryTest extends TestCase {
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $sourceMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $channelMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $eventEnvelopeMock;
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $eventEnvelopeProperties;
	
	/** @var AmqpDeliveredEventProcessPromiseFactory */
	private $factory;
	
	private $deliveryTag = 1;
	
	protected function setUp() {
		$this->sourceMock = $this->createMock(BasicAmqpEventStreamSource::class);
		
		$this->channelMock = $this->createMock(AMQPChannel::class);
		$this->sourceMock->method('getChannel')->willReturn($this->channelMock);
		
		$this->eventEnvelopeMock = $this->createMock(BasicEventEnvelope::class);
		
		$this->eventEnvelopeProperties = $this->createMock(AmqpEventEnvelopeProperties::class);
		$this->eventEnvelopeProperties->method('getDeliveryTag')->willReturn($this->deliveryTag);
		$this->eventEnvelopeMock->method('getProperties')->willReturn($this->eventEnvelopeProperties);
		
		$this->factory = new AmqpDeliveredEventProcessPromiseFactory($this->sourceMock);
	}
	
	public function testCreatedProcessPromiseResolve() {
		$promise = $this->factory->create($this->eventEnvelopeMock);
		$this->channelMock->expects($this->once())->method('basic_ack')->with($this->deliveryTag);
		$promise->resolve(null);
		$this->runPromiseQueue();
	}
	
	public function testCreatedProcessPromiseForCancel() {
		$promise = $this->factory->create($this->eventEnvelopeMock);
		$this->channelMock->expects($this->once())->method('basic_reject')->with($this->deliveryTag, true);
		$promise->cancel();
		$this->runPromiseQueue();
	}
	
	public function testCreatedProcessPromiseReject() {
		$promise = $this->factory->create($this->eventEnvelopeMock);
		$this->channelMock->expects($this->once())->method('basic_reject')->with($this->deliveryTag, false);
		$promise->reject(new \Exception("error"));
		$this->runPromiseQueue();
	}
	
	protected function runPromiseQueue() {
		\GuzzleHttp\Promise\queue()->run();
	}
	
}
