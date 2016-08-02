<?php

namespace SAREhub\Client\Amqp;

use SAREhub\Client\Event\EventEnvelopeProperties;

class AmqpEventEnvelopeProperties implements EventEnvelopeProperties {
	
	private $routingKey;
	
	/**
	 * @return
	 */
	public function getRoutingKey() {
		return $this->routingKey;
	}
}