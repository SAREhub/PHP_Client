<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Event\EventEnvelopeProperties;

class AmqpEventEnvelopeProperties implements EventEnvelopeProperties {
	
	/** @var RoutingKey */
	private $routingKey;
	
	/** @var string */
	private $replyTo;
	
	/** @var string */
	private $correlationId;
	
	/** @var string */
	private $priority;
	
	/** @var null|array */
	private $deliveryProperties = null;
	
	public static function createFromDeliveredMessage(AMQPMessage $message) {
		$object = new self();
		$messageProperties = $message->get_properties();
		$object->routingKey = RoutingKey::createFromString($message->delivery_info['routing_key']);
		$object->replyTo = isset($messageProperties['reply_to']) ? $messageProperties['reply_to'] : '';
		$object->correlationId = isset($messageProperties['correlation_id']) ? $messageProperties['correlation_id'] : '';
		$object->priority = isset($messageProperties['priority']) ? $messageProperties['priority'] : 0;
		$object->deliveryProperties = $message->delivery_info;
		return $object;
	}
	
	/**
	 * Returns routing key as string in AMQP routing key format
	 * @return string
	 * @throws EmptyRoutingKeyAmqpException When routing key is null or empty
	 */
	public function getRoutingKeyAsString() {
		if ($this->hasRoutingKey() && !$this->routingKey->isEmpty()) {
			return (string)$this->getRoutingKey();
		}
		
		throw new EmptyRoutingKeyAmqpException();
	}
	
	/**
	 * @return bool
	 */
	public function hasRoutingKey() {
		return $this->routingKey !== null;
	}
	
	/**
	 * @return RoutingKey
	 */
	public function getRoutingKey() {
		return $this->routingKey;
	}
	
	/**
	 * @param RoutingKey $routingKey
	 */
	public function setRoutingKey(RoutingKey $routingKey) {
		$this->routingKey = $routingKey;
	}
	
	/**
	 * @return array
	 */
	public function getDeliveryProperties() {
		return $this->deliveryProperties;
	}
	
	/**
	 * @return bool
	 */
	public function hasDeliveryProperties() {
		return $this->deliveryProperties !== null;
	}
	
	/**
	 * @return array
	 */
	public function toAmqpMessageProperties() {
		$properties = [];
		if ($this->hasReplyTo()) {
			$properties['reply_to'] = $this->getReplyTo();
			$properties['correlation_id'] = $this->getCorrelationId();
		} else if ($this->hasCorrelationId()) {
			$properties['correlation_id'] = $this->getCorrelationId();
		}
		
		if ($this->hasPriority()) {
			$properties['priority'] = $this->getPriority();
		}
		
		return $properties;
	}
	
	/**
	 * @return bool
	 */
	public function hasReplyTo() {
		return !empty($this->replyTo);
	}
	
	/**
	 * @return string
	 */
	public function getReplyTo() {
		return $this->replyTo;
	}
	
	/**
	 * @param string $replyTo
	 * @param string $correlationId
	 */
	public function setReplyTo($replyTo, $correlationId = '') {
		$this->replyTo = $replyTo;
		$this->correlationId = $correlationId;
	}
	
	/**
	 * @return string
	 */
	public function getCorrelationId() {
		return $this->correlationId;
	}
	
	/**
	 * @param string $correlationId
	 */
	public function setCorrelationId($correlationId) {
		$this->correlationId = $correlationId;
	}
	
	/**
	 * @return bool
	 */
	public function hasCorrelationId() {
		return !empty($this->correlationId);
	}
	
	/**
	 * @return bool
	 */
	public function hasPriority() {
		return $this->priority > 0;
	}
	
	/**
	 * @return int
	 */
	public function getPriority() {
		return $this->priority;
	}
	
	/**
	 * @param int $priority
	 */
	public function setPriority($priority) {
		$this->priority = $priority;
	}
}