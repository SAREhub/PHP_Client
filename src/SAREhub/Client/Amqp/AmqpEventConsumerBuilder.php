<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

/**
 * That builder can build amqp queue event consumer callback with given context
 */
class AmqpEventConsumerBuilder {
	
	/** @var BasicAmqpEventStreamSource */
	private $source;
	
	/** @var AmqpEventEnvelopeFactory */
	private $eventEnvelopeFactory;
	
	public function source(BasicAmqpEventStreamSource $source) {
		$this->source = $source;
	}
	
	public function eventEnvelopeFactory(AmqpEventEnvelopeFactory $factory) {
		$this->eventEnvelopeFactory = $factory;
		return $this;
	}
	
	/**
	 * @return \Closure
	 * @throws ValidationException When one of required context parameter isn't defined
	 */
	public function build() {
		$source = $this->source;
		v::notEmpty()->setName('source')->check($source);
		
		$eventEnvelopeFactory = $this->eventEnvelopeFactory;
		v::notEmpty()->setName('eventEnvelopeFactory')->check($eventEnvelopeFactory);
		
		return function (AMQPMessage $amqpMessage) use ($source, $eventEnvelopeFactory) {
			$eventEnvelope = $eventEnvelopeFactory->createFromDeliveredMessage($amqpMessage);
			$source->getSink()->write($eventEnvelope);
		};
	}
}