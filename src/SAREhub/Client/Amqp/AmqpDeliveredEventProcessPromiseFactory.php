<?php

namespace SAREhub\Client\Amqp;

use GuzzleHttp\Promise\CancellationException;
use GuzzleHttp\Promise\Promise;
use SAREhub\Client\Event\EventEnvelope;

class AmqpDeliveredEventProcessPromiseFactory {
	
	/** @var BasicAmqpEventStreamSource */
	protected $source;
	
	public function __construct(BasicAmqpEventStreamSource $source) {
		$this->source = $source;
	}
	
	public function create(EventEnvelope $eventEnvelope) {
		$source = $this->source;
		$promise = new Promise();
		$promise->then(function () use ($source, $eventEnvelope) {
			$source->getChannel()->basic_ack($eventEnvelope->getProperties()->getDeliveryTag());
		}, function ($error) use ($source, $eventEnvelope) {
			$requeue = $error instanceof CancellationException;
			$source->getChannel()->basic_reject($eventEnvelope->getProperties()->getDeliveryTag(), $requeue);
		});
		
		return $promise;
	}
}