<?php

namespace SAREhub\Client\Amqp;

/**
 * Context for AMQP event consumer callback
 */
class AmqpEventConsumerContext {
	
	public $source;
	public $deserializationService;
	public $processPromiseFactory;
}