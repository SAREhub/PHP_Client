<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\BasicEventEnvelope;
use SAREhub\Client\Event\Event;
use SAREhub\Client\Event\JsonEventSerializationService;

class BasicAmqpEventStreamSinkTest extends TestCase {
	
	private $channelMock;
	private $eventMock;
	private $eventEnvelopeMock;
	private $eventSerializationService;
	
	public function setUp() {
		$this->channelMock = $this->getMockBuilder(AMQPChannel::class)
		  ->disableOriginalConstructor()
		  ->setMethods(['basic_publish'])
		  ->getMock();
		
		$this->eventMock = $this->createMock(Event::class);
		
		$this->eventEnvelopeMock = $this->createMock(BasicEventEnvelope::class);
		$this->eventEnvelopeMock->method('getEvent')->willReturn($this->eventMock);
		$this->eventEnvelopeMock->expects($this->once())->method('markAsProcessed');
		$this->eventEnvelopeMock->expects($this->never())->method('markAsProcessedExceptionally');
		
		$this->eventSerializationService = $this->createMock(JsonEventSerializationService::class);
		$this->eventSerializationService->method('serialize')
		  ->with($this->identicalTo($this->eventMock))
		  ->willReturn(json_encode(['type' => 'test']));
	}
	
	public function testWrite() {
		$eventEnvelopePropertiesMock = $this->createMock(AmqpEventEnvelopeProperties::class);
		$eventEnvelopePropertiesMock->method('getRoutingKeyAsString')->willReturn('part1.part2');
		$this->eventEnvelopeMock->method('getProperties')->willReturn($eventEnvelopePropertiesMock);
		
		$this->channelMock->expects($this->once())
		  ->method('basic_publish')
		  ->with(
			$this->callback(function (AMQPMessage $message) {
				return $message->getBody() === json_encode(['type' => 'test']);
			}),
			'exchange',
			'part1.part2'
		  );
		
		$sink = new BasicAmqpEventStreamSink($this->channelMock, 'exchange', $this->eventSerializationService);
		$sink->write($this->eventEnvelopeMock);
	}
	
	public function testWriteWithReplyTo() {
		$eventEnvelopePropertiesMock = $this->createMock(AmqpEventEnvelopeProperties::class);
		$eventEnvelopePropertiesMock->method('toAmqpMessageProperties')->willReturn([
		  'correlation_id' => 'test_id',
		  'reply_to' => 'test_to'
		]);
		$eventEnvelopePropertiesMock->method('getRoutingKeyAsString')->willReturn('part1.part2');
		$this->eventEnvelopeMock->method('getProperties')->willReturn($eventEnvelopePropertiesMock);
		
		$this->channelMock->expects($this->once())
		  ->method('basic_publish')
		  ->with(
			$this->callback(function (AMQPMessage $message) {
				return $message->getBody() === json_encode(['type' => 'test']) &&
				$message->has('correlation_id') &&
				$message->has('reply_to');
			}),
			'exchange',
			'part1.part2'
		  );
		
		$sink = new BasicAmqpEventStreamSink($this->channelMock, 'exchange', $this->eventSerializationService);
		$sink->write($this->eventEnvelopeMock);
	}
	
	public function testWriteResponseForReplyTo() {
		$eventEnvelopePropertiesMock = $this->createMock(AmqpEventEnvelopeProperties::class);
		$eventEnvelopePropertiesMock->method('toAmqpMessageProperties')->willReturn([
		  'correlation_id' => 'test_id'
		]);
		$eventEnvelopePropertiesMock->method('getRoutingKeyAsString')->willReturn('part1.part2');
		$this->eventEnvelopeMock->method('getProperties')->willReturn($eventEnvelopePropertiesMock);
		
		$this->channelMock->expects($this->once())
		  ->method('basic_publish')
		  ->with(
			$this->callback(function (AMQPMessage $message) {
				return $message->getBody() === json_encode(['type' => 'test']) && $message->has('correlation_id');
			}),
			'exchange',
			'part1.part2'
		  );
		
		$sink = new BasicAmqpEventStreamSink($this->channelMock, 'exchange', $this->eventSerializationService);
		$sink->write($this->eventEnvelopeMock);
	}
	
}
