<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Event\BasicEventEnvelope;
use SAREhub\Client\Event\EventDeserializationService;
use SAREhub\Client\Event\EventEnvelope;

class AmqpEventEnvelopeFactory {
	
	/** @var EventDeserializationService */
	protected $eventDeserializationService;
	
	/** @var AmqpEventEnvelopePropertiesFactory */
	protected $propertiesFactory;
	
	/** @var AmqpDeliveredEventProcessPromiseFactory */
	protected $processPromiseFactory;
	
	/**
	 * @return AmqpEventEnvelopeFactory
	 */
	public static function factory() {
		return new self();
	}
	
	/**
	 * @param EventDeserializationService $service
	 * @return $this
	 */
	public function eventDeserializationService(EventDeserializationService $service) {
		$this->eventDeserializationService = $service;
		return $this;
	}
	
	/**
	 * @param AmqpEventEnvelopePropertiesFactory $propertiesFactory
	 * @return $this
	 */
	public function propertiesFactory(AmqpEventEnvelopePropertiesFactory $propertiesFactory) {
		$this->propertiesFactory = $propertiesFactory;
		return $this;
	}
	
	/**
	 * @param AmqpDeliveredEventProcessPromiseFactory $processPromiseFactory
	 * @return $this
	 */
	public function processPromiseFactory(AmqpDeliveredEventProcessPromiseFactory $processPromiseFactory) {
		$this->processPromiseFactory = $processPromiseFactory;
		return $this;
	}
	
	/**
	 * @param AMQPMessage $amqpMessage
	 * @return EventEnvelope
	 */
	public function createFromDeliveredMessage(AMQPMessage $amqpMessage) {
		$event = $this->eventDeserializationService->deserialize($amqpMessage->getBody());
		$eventEnvelope = new BasicEventEnvelope($event);
		
		$eventEnvelopeProperties = $this->propertiesFactory->createFromMessage($amqpMessage);
		$eventEnvelope->setProperties($eventEnvelopeProperties);
		
		$processPromise = $this->processPromiseFactory->create($eventEnvelope);
		$eventEnvelope->setProcessPromise($processPromise);
		
		return $eventEnvelope;
	}
}