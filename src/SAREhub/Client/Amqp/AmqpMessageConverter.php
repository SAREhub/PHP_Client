<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Amqp\AmqpMessageHeaders as AMH;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Message;

class AmqpMessageConverter {
	
	/**
	 * Converts AMQP message to Client Message
	 * @param AMQPMessage $message
	 * @return Message
	 */
	public function convertFrom(AMQPMessage $message) {
		return BasicMessage::withBody($message->getBody())
		  ->setHeaders([
			AMH::CONSUMER_TAG => $message->get('consumer_tag'),
			AMH::DELIVERY_TAG => $message->get('delivery_tag'),
			AMH::REDELIVERED => $message->get('redelivered'),
			AMH::EXCHANGE => $message->get('exchange'),
		    AMH::ROUTING_KEY => RoutingKey::createFromString($message->get('routing_key')),
			AMH::CONTENT_TYPE => $message->get('content_type'),
		    AMH::CONTENT_ENCODING => $message->has('content_encoding') ? $message->get('content_encoding') : null,
			AMH::REPLY_TO => $message->get('reply_to'),
			AMH::CORRELATION_ID => $message->get('correlation_id'),
			AMH::PRIORITY => $message->get('priority')
		  ]);
	}
	
	/**
	 * Converts Client Message to AMQP message
	 * @param Message $message
	 * @return AMQPMessage
	 */
	public function convertTo(Message $message) {
		$properties = [];
		foreach (AmqpMessageHeaders::getPropertyHeaders() as $header) {
			if ($message->hasHeader($header)) {
				$properties[AmqpMessageHeaders::getMessagePropertyName($header)] = $message->getHeader($header);
			}
		}
		return new AMQPMessage($message->getBody(), $properties);
	}
}