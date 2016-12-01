<?php

namespace unit\SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpMessageConverter;
use SAREhub\Client\Amqp\AmqpMessageHeaders;
use SAREhub\Client\Message\Message;

class AmqpMessageConverterTest extends TestCase {
	
	/**
	 * @var AmqpMessageConverter
	 */
	private $converter;
	
	/**
	 * @var AMQPMessage
	 */
	private $amqpMessage;
	
	/**
	 * @var Message
	 */
	private $convertedMessage;
	
	protected function setUp() {
		$this->converter = new AmqpMessageConverter();
		
		$this->amqpMessage = new AMQPMessage('message_body', [
		  'content_type' => 'p_content_type',
		  'content_encoding' => 'content_encoding',
		  'reply_to' => 'p_reply_to',
		  'correlation_id' => 'p_correlation_id',
		  'priority' => 'p_priority'
		]);
		$this->amqpMessage->delivery_info = [
		  'consumer_tag' => 'd_consumer_tag',
		  'delivery_tag' => 'd_delivery_tag',
		  'redelivered' => 'd_redelivered',
		  'exchange' => 'd_exchange',
		  'routing_key' => 'd_routing_key',
		];
		
		$this->convertedMessage = $this->converter->convert($this->amqpMessage);
	}
	
	public function testConvertThenBody() {
		$this->assertEquals($this->amqpMessage->getBody(), $this->convertedMessage->getBody());
	}
	
	public function testConvertThenConsumerTagHeader() {
		$this->assertHeader('consumer_tag', AmqpMessageHeaders::CONSUMER_TAG);
	}
	
	public function testConvertThenDeliveryTagHeader() {
		$this->assertHeader('delivery_tag', AmqpMessageHeaders::DELIVERY_TAG);
	}
	
	public function testConvertThenRedeliveredHeader() {
		$this->assertHeader('redelivered', AmqpMessageHeaders::REDELIVERED);
	}
	
	public function testConvertThenExchangeHeader() {
		$this->assertHeader('exchange', AmqpMessageHeaders::EXCHANGE);
	}
	
	public function testConvertThenRoutingKeyHeader() {
		$this->assertHeader('routing_key', AmqpMessageHeaders::ROUTING_KEY);
	}
	
	public function testConvertThenContentTypeHeader() {
		$this->assertHeader('content_type', AmqpMessageHeaders::CONTENT_TYPE);
	}
	
	public function testConvertThenContentEncodingHeader() {
		$this->assertHeader('content_encoding', AmqpMessageHeaders::CONTENT_ENCODING);
	}
	
	public function testConvertThenReplyToHeader() {
		$this->assertHeader('reply_to', AmqpMessageHeaders::REPLY_TO);
	}
	
	public function testConvertThenCorrelationIdHeader() {
		$this->assertHeader('correlation_id', AmqpMessageHeaders::CORRELATION_ID);
	}
	
	public function testConvertThenPriorityHeader() {
		$this->assertHeader('priority', AmqpMessageHeaders::PRIORITY);
	}
	
	private function assertHeader($property, $header) {
		$current = $this->convertedMessage->getHeader($header);
		$this->assertEquals($this->amqpMessage->get($property), $current);
	}
}
