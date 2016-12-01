<?php

namespace SAREhub\Client\Amqp;

/**
 * Enum for all supported AMQP headers sets in Exchange messages.
 */
class AmqpMessageHeaders {
	
	const CONSUMER_TAG = 'amqp_consumer_tag';
	const DELIVERY_TAG = 'amqp_delivery_tag';
	const REDELIVERED = 'amqp_redelivered';
	const EXCHANGE = 'amqp_exchange';
	const ROUTING_KEY = 'amqp_routing_key';
	
	const CONTENT_TYPE = 'amqp_content_type';
	const CONTENT_ENCODING = 'amqp_content_encoding';
	const REPLY_TO = 'amqp_reply_to';
	const CORRELATION_ID = 'amqp_correlation_id';
	const PRIORITY = 'amqp_priority';
}