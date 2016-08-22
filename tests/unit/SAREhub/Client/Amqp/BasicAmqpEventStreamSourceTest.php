<?php

namespace SAREhub\Client\Amqp;

use GuzzleHttp\Promise\CancellationException;
use PhpAmqpLib\Channel\AMQPChannel;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\BasicEventEnvelope;

class BasicAmqpEventStreamSourceTest extends TestCase {
	
	private $channelMock;
	private $consumerBuilderMock;
	private $consumerMock;
	
	private $sourceMock;
	private $eventEnvelopeMock;
	
	public function testCreate() {
		$this->consumerBuilderMock->expects($this->once())->method('source');
		$source = new BasicAmqpEventStreamSource($this->channelMock, [
		  'queueName' => 'test',
		  'consumerTag' => 'tag',
		  'consumerBuilder' => $this->consumerBuilderMock
		]);
		
		$this->assertEquals('test', $source->getQueue());
		$this->assertEquals('tag', $source->getConsumerTag());
	}
	
	public function testFlowOpen() {
		$source = new BasicAmqpEventStreamSource($this->channelMock, [
		  'queueName' => 'test',
		  'consumerBuilder' => $this->consumerBuilderMock
		]);
		
		$source->flow();
		$this->assertInstanceOf(\Generator::class, $source->getFlowControl());
		$this->assertTrue($source->isInFlowMode());
	}
	
	public function testFlow() {
		$this->channelMock->expects($this->once())
		  ->method('basic_consume')
		  ->with('test', '', false, false, false, false, $this->identicalTo($this->consumerMock));
		
		$this->channelMock->expects($this->once())->method('wait');
		$this->channelMock->callbacks = [1]; // needs for count in flow loop
		$source = new BasicAmqpEventStreamSource($this->channelMock, [
		  'queueName' => 'test',
		  'consumerBuilder' => $this->consumerBuilderMock
		]);
		
		$source->flow();
		$source->getFlowControl()->next();
	}
	
	public function testStopFlow() {
		$this->channelMock->expects($this->once())->method('basic_cancel')->with('');
		$this->channelMock->expects($this->once())->method('wait');
		
		$this->channelMock->callbacks = [1]; // needs for count in flow loop
		$source = new BasicAmqpEventStreamSource($this->channelMock, [
		  'queueName' => 'test',
		  'consumerBuilder' => $this->consumerBuilderMock
		]);
		
		$source->flow();
		$flowControl = $source->getFlowControl();
		$flowControl->next();
		$source->stopFlow();
		$this->assertFalse($source->isInFlowMode());
		$flowControl->next();
	}
	
	/**
	 * @expectedException \SAREhub\Client\Amqp\AmqpException
	 */
	public function testFlowOpenWhenOpened() {
		$source = new BasicAmqpEventStreamSource($this->channelMock, [
		  'queueName' => 'test',
		  'consumerBuilder' => $this->consumerBuilderMock
		]);
		
		$source->flow();
		$source->flow();
	}
	
	public function testDefaultProcessPromiseFactoryForResolve() {
		$eventEnvelopeProperties = $this->createEventEnvelopePropertiesMock();
		$eventEnvelopeProperties->method('getDeliveryTag')->willReturn('testTag');
		$this->eventEnvelopeMock->method('getProperties')->willReturn($eventEnvelopeProperties);
		
		$processPromiseFactory = BasicAmqpEventStreamSource::createDefaultProcessPromiseFactory();
		$promise = $processPromiseFactory($this->sourceMock, $this->eventEnvelopeMock);
		$this->channelMock->expects($this->once())->method('basic_ack')->with('testTag');
		$promise->resolve(null);
		\GuzzleHttp\Promise\queue()->run();
	}
	
	private function createEventEnvelopePropertiesMock() {
		return $this->getMockBuilder(AmqpEventEnvelopeProperties::class)
		  ->disableOriginalConstructor()
		  ->getMock();
	}
	
	public function testDefaultProcessPromiseFactoryForCancel() {
		$eventEnvelopeProperties = $this->createEventEnvelopePropertiesMock();
		$eventEnvelopeProperties->method('getDeliveryTag')->willReturn('testTag');
		$this->eventEnvelopeMock->method('getProperties')->willReturn($eventEnvelopeProperties);
		
		$processPromiseFactory = BasicAmqpEventStreamSource::createDefaultProcessPromiseFactory();
		$promise = $processPromiseFactory($this->sourceMock, $this->eventEnvelopeMock);
		$this->channelMock->expects($this->once())->method('basic_reject')->with('testTag', true);
		$promise->reject(new CancellationException("cancel"));
		\GuzzleHttp\Promise\queue()->run();
	}
	
	public function testDefaultProcessPromiseFactoryForReject() {
		$eventEnvelopeProperties = $this->createEventEnvelopePropertiesMock();
		$eventEnvelopeProperties->method('getDeliveryTag')->willReturn('testTag');
		$this->eventEnvelopeMock->method('getProperties')->willReturn($eventEnvelopeProperties);
		
		$processPromiseFactory = BasicAmqpEventStreamSource::createDefaultProcessPromiseFactory();
		$promise = $processPromiseFactory($this->sourceMock, $this->eventEnvelopeMock);
		$this->channelMock->expects($this->once())->method('basic_reject')->with('testTag', false);
		$promise->reject(new \Exception("error"));
		\GuzzleHttp\Promise\queue()->run();
	}
	
	protected function setUp() {
		$this->channelMock = $this->getMockBuilder(AMQPChannel::class)
		  ->disableOriginalConstructor()
		  ->getMock();
		
		$this->consumerBuilderMock = $this->getMockBuilder(AmqpEventConsumerBuilder::class)->getMock();
		$this->consumerMock = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
		$this->consumerBuilderMock->method('build')->willReturn($this->consumerMock);
		
		$this->sourceMock = $this->getMockBuilder(BasicAmqpEventStreamSource::class)
		  ->disableOriginalConstructor()
		  ->getMock();
		
		$this->sourceMock->method('getChannel')->willReturn($this->channelMock);
		
		$this->eventEnvelopeMock = $this->getMockBuilder(BasicEventEnvelope::class)
		  ->disableOriginalConstructor()
		  ->getMock();
	}
}
