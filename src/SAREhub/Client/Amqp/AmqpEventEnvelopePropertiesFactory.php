<?php

namespace SAREhub\Client\Amqp;

class AmqpEventEnvelopePropertiesFactory {
	
	public function createFromMessage($amqpMessage) {
		$properties = new AmqpEventEnvelopeProperties();
		
		$deliveryInfo = $amqpMessage->delivery_info;
		$properties->setDeliveryProperties($deliveryInfo);
		$properties->setRoutingKey(new RoutingKey($deliveryInfo['routing_key']));
		
		$messageProperties = $amqpMessage->get_properties();
		$properties->setReplyTo(isset($messageProperties['reply_to']) ? $messageProperties['reply_to'] : '');
		$properties->setCorrelationId(isset($messageProperties['correlation_id']) ? $messageProperties['correlation_id'] : '');
		$properties->setPriority(isset($messageProperties['priority']) ? $messageProperties['priority'] : 0);
		
		
		return $properties;
	}
}