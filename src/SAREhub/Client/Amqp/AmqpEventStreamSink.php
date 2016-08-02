<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Event\EventEnvelope;
use SAREhub\Client\Event\EventSerializationService;
use SAREhub\Client\Event\EventStreamSink;
use SAREhub\Client\Event\EventStreamSource;

class AmqpEventStreamSink implements EventStreamSink {
	
	const EXCHANGE_PREFIX = "PC";
	
	protected $channel;
	protected $systemName;
	protected $exchangeName;
	protected $eventSerializationService;
	
	public function __construct(AMQPChannel $channel, $systemName, EventSerializationService $eventSerializationService) {
		$this->channel = $channel;
		$this->systemName = $systemName;
		$this->exchangeName = self::EXCHANGE_PREFIX.$systemName;
		$this->eventSerializationService = $eventSerializationService;
	}
	
	public function write(EventEnvelope $eventEnvelope) {
		try {
			$messageBody = $this->eventSerializationService->serialize($eventEnvelope->getEvent());
			$message = new AMQPMessage($messageBody);
			
			$messageProperties = $eventEnvelope->getProperties();
			$this->channel->basic_publish($message, $this->getExchangeName(), );
		} catch (\Exception $e) {
			
		}
	}
	
	public function getExchangeName() {
		return $this->exchangeName;
	}
	
	public function onPipe(EventStreamSource $source) {
		
	}
	
	public function onUnpipe(EventStreamSource $source) {
		
	}
}