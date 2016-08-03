<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Event\EventEnvelope;
use SAREhub\Client\Event\EventSerializationService;
use SAREhub\Client\Event\EventStreamSink;
use SAREhub\Client\Event\EventStreamSource;

class AmqpEventStreamSink implements EventStreamSink {
	
	protected $channel;
	protected $exchangeName;
	protected $eventSerializationService;
	
	public function __construct(AMQPChannel $channel,
	                            $exchangeName,
	                            EventSerializationService $eventSerializationService) {
		
		$this->channel = $channel;
		$this->exchangeName = $exchangeName;
		$this->eventSerializationService = $eventSerializationService;
	}
	
	public function write(EventEnvelope $eventEnvelope) {
		try {
			$messageBody = $this->eventSerializationService->serialize($eventEnvelope->getEvent());
			/** @var AmqpEventEnvelopeProperties $messageProperties */
			$messageProperties = $eventEnvelope->getProperties();
			$this->channel->basic_publish(new AMQPMessage(
			  $messageBody, $messageProperties->toAmqpMessageProperties()),
			  $this->getExchangeName(),
			  $messageProperties->getRoutingKeyAsString()
			);
			$eventEnvelope->markAsProcessed();
		} catch (AmqpException $e) {
			$eventEnvelope->markAsProcessedExceptionally($e);
		}
	}
	
	public function getExchangeName() {
		return $this->exchangeName;
	}
	
	public function onPipe(EventStreamSource $source) {
		
	}
	
	public function onUnpipe(EventStreamSource $source) {
		
	}
	
	public function getChannel() {
		return $this->channel;
	}
}