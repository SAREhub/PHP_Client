<?php

namespace SAREhub\Client\Amqp;

use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\Processor;

class AmqpProducer implements Processor {
	
	/**
	 * @var AmqpChannelWrapper
	 */
	private $channel;
	
	/**
	 * @var AmqpMessageConverter
	 */
	private $converter;
	
	/**
	 * @return AmqpProducer
	 */
	public static function newInstance() {
		return new self();
	}
	
	public function withChannel(AmqpChannelWrapper $channel) {
		$this->channel = $channel;
		return $this;
	}
	
	public function withConverter(AmqpMessageConverter $converter) {
		$this->converter = $converter;
		return $this;
	}
	
	public function process(Exchange $exchange) {
		$in = $exchange->getIn();
		$message = $this->converter->convertTo($in);
		$exchange = $in->getHeader(AmqpMessageHeaders::EXCHANGE);
		$routingKey = $in->getHeader(AmqpMessageHeaders::ROUTING_KEY);
		$this->getChannel()->publish($message, $exchange, $routingKey);
	}
	
	/**
	 * @return AmqpChannelWrapper
	 */
	public function getChannel() {
		return $this->channel;
	}
}