<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;
use SAREhub\Client\Event\BasicEventEnvelope;
use SAREhub\Client\Event\EventDeserializationService;

/**
 * That builder can build amqp queue event consumer callback with given context
 */
class AmqpEventConsumerBuilder {
	
	private $source;
	private $deserializationService;
	private $processPromiseFactory;
	
	public function source(BasicAmqpEventStreamSource $source) {
		$this->source = $source;
		return $this;
	}
	
	public function deserializationService(EventDeserializationService $service) {
		$this->deserializationService = $service;
		return $this;
	}
	
	public function processPromiseFactory(callable $factory) {
		$this->processPromiseFactory = $factory;
		return $this;
	}
	
	/**
	 * @return \Closure
	 * @throws ValidationException When one of required context parameter isn't defined
	 */
	public function build() {
		$source = $this->source;
		v::notEmpty()->setName('source')->check($source);
		
		$deserializationService = $this->deserializationService;
		v::notEmpty()->setName('deserializationService')->check($deserializationService);
		
		$processPromiseFactory = $this->processPromiseFactory;
		v::notEmpty()->setName('processPromiseFactory')->check($processPromiseFactory);
		
		return function (AMQPMessage $message) use ($source, $deserializationService, $processPromiseFactory) {
			$event = $deserializationService->deserialize($message->getBody());
			$eventEnvelopeProperties = AmqpEventEnvelopeProperties::createFromDeliveredMessage($message);
			$eventEnvelope = new BasicEventEnvelope($event, $eventEnvelopeProperties);
			$eventEnvelope->setProcessPromise($processPromiseFactory($source, $eventEnvelope));
			$source->getSink()->write($eventEnvelope);
		};
	}
}