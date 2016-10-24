<?php

namespace SAREhub\Client\Amqp;

use GuzzleHttp\Promise\CancellationException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\IO\SocketIO;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\BasicEventEnvelope;

class BasicAmqpEventStreamSourceTest extends TestCase {
	
	private $socketMock;
	private $connectionMock;
	private $channelMock;
	private $consumerBuilderMock;
	private $consumerMock;
	
	private $sourceMock;
	private $eventEnvelopeMock;
	
	/**
	 * @var BasicAmqpEventStreamSource
	 */
	private $source;
	
	protected function setUp() {
		$this->channelMock = $this->createMock(AMQPChannel::class);
		$this->connectionMock = $this->createMock(AMQPStreamConnection::class);
		$this->channelMock->method('getConnection')->willReturn($this->connectionMock);
		$this->socketMock = $this->createMock(SocketIO::class);
		$this->connectionMock->method('getSocket')->willReturn($this->socketMock);
		
		$this->consumerBuilderMock = $this->createMock(AmqpEventConsumerBuilder::class);
		$this->consumerMock = $this->createPartialMock(\stdClass::class, ['__invoke']);
		$this->consumerBuilderMock->method('build')->willReturn($this->consumerMock);
		
		$this->sourceMock = $this->createMock(BasicAmqpEventStreamSource::class);
		$this->sourceMock->method('getChannel')->willReturn($this->channelMock);
		
		$this->eventEnvelopeMock = $this->createMock(BasicEventEnvelope::class);
		
		$this->source = $source = new BasicAmqpEventStreamSource($this->channelMock, [
		  'queueName' => 'test',
		  'consumerBuilder' => $this->consumerBuilderMock
		]);
	}
	
	public function testCreateThenConsumerBuilderCallSource() {
		$this->consumerBuilderMock->expects($this->once())->method('source');
		new BasicAmqpEventStreamSource($this->channelMock, [
		  'queueName' => 'test',
		  'consumerBuilder' => $this->consumerBuilderMock
		]);
	}
	
	public function testCreateThenQueueSets() {
		$source = new BasicAmqpEventStreamSource($this->channelMock, [
		  'queueName' => 'test',
		  'consumerBuilder' => $this->consumerBuilderMock
		]);
		
		$this->assertEquals('test', $source->getQueue());
	}
	
	public function testCreateWithConsumerTagThenConsumerTagSets() {
		$source = new BasicAmqpEventStreamSource($this->channelMock, [
		  'queueName' => 'test',
		  'consumerTag' => 'tag',
		  'consumerBuilder' => $this->consumerBuilderMock
		]);
		$this->assertEquals('tag', $source->getConsumerTag());
	}
	
	public function testFlowOpenThenGetFlowControlWillReturnGeneratorInstance() {
		$this->source->flow();
		$this->assertInstanceOf(\Generator::class, $this->source->getFlowControl());
	}
	
	public function testFlowOpenThenIsInFlowModeReturnTrue() {
		$this->source->flow();
		$this->assertTrue($this->source->isInFlowMode());
	}
	
	public function testFlowControlNextWhenFirstTimeThenBasicConsume() {
		$this->channelMock->expects($this->once())->method('basic_consume')
		  ->with('test', '', false, false, false, false, $this->identicalTo($this->consumerMock));
		$this->source->flow();
		$this->source->getFlowControl()->next();
	}
	
	public function testFlowControlNextWhenNextCallTimeThenNotBasicConsume() {
		$this->source->flow();
		$this->source->getFlowControl()->next();
		$this->channelMock->expects($this->never())->method('basic_consume');
		$this->source->getFlowControl()->next();
	}
	
	public function testFlowControlNextThenChannelWait() {
		$this->socketMock->method('select')->willReturn(1);
		$this->channelMock->expects($this->once())->method('wait')
		  ->with(null, true, BasicAmqpEventStreamSource::DEFAULT_TIMEOUT);
		$this->channelMock->callbacks = [1]; // needs for count in flow loop
		$this->source->flow();
		$this->source->getFlowControl()->next();
	}
	
	public function testFlowControlNextWhenSocketSelectReturnZeroThenNotWait() {
		$this->channelMock->expects($this->never())->method('wait');
		$this->channelMock->callbacks = [1]; // needs for count in flow loop
		$this->socketMock->method('select')->willReturn(0);
		$this->source->flow();
		$this->source->getFlowControl()->next();
	}
	
	public function testStopFlowWhenFlowThenIsNotInFlowModeReturnFalse() {
		$this->source->flow();
		$this->source->stopFlow();
	}
	
	public function testStopFlowThenChannelCancel() {
		$this->channelMock->expects($this->once())->method('basic_cancel')->with('');
		$this->source->flow();
		$this->source->stopFlow();
	}
	
	public function testFlowOpenWhenOpened() {
		$this->expectException(AmqpException::class);
		$this->source->flow();
		$this->source->flow();
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
	
	
}
