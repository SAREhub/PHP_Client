<?php

namespace SAREhub\Client\Amqp;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;
use SAREhub\Client\Event\EventDeserializationService;

/**
 * That builder can build amqp queue event consumer callback with given context
 */
class AmqpEventConsumerBuilder {
	
	private $consumerContext;
	
	public function __construct() {
		$this->consumerContext = new AmqpEventConsumerContext();
	}
	
	public function source(BasicAmqpEventStreamSource $source) {
		$this->consumerContext->source = $source;
		return $this;
	}
	
	public function deserializationService(EventDeserializationService $service) {
		$this->consumerContext->deserializationService = $service;
		return $this;
	}
	
	public function processPromiseFactory(callable $factory) {
		$this->consumerContext->processPromiseFactory = $factory;
		return $this;
	}
	
	/**
	 * @return \Closure
	 * @throws ValidationException When one of required context parameter isn't defined
	 */
	public function build() {
		$context = new AmqpEventConsumerContext();
		
		$context->source = $this->consumerContext->source;
		v::notEmpty()->setName('source')->check($context->source);
		
		$context->deserializationService = $this->consumerContext->deserializationService;
		v::notEmpty()->setName('deserializationService')->check($context->deserializationService);
		
		$context->processPromiseFactory = $this->consumerContext->processPromiseFactory;
		v::notEmpty()->setName('processPromiseFactory')->check($context->processPromiseFactory);
		
		
		return function (AMQPMessage $message) use ($context) {
			if (!$context->source->hasStopPromise()) {
				$event = $context->deserializationService->deserialize($message->getBody());
				$eventEnvelopeProperties = AmqpEventEnvelopeProperties::createFromDeliveredMessage($message);
				$eventEnvelope = new BasicEventEnvelope($event, $eventEnvelopeProperties);
				$eventEnvelope->setProcessPromise($context->processPromiseFactory($context->source, $eventEnvelope));
				$context->source->getSink()->write($eventEnvelope);
			}
		};
	}
}