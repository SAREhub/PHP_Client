<?php

namespace unit\SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Amqp\AmqpMessageConverter;
use SAREhub\Client\Amqp\AmqpMessageHeaders;
use SAREhub\Client\Message\Message;

class AmqpMessageConverterTest extends TestCase
{

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

    protected function setUp()
    {
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

        $this->convertedMessage = $this->converter->convertFrom($this->amqpMessage);
    }

    public function testConvertToThenBody()
    {
        $this->assertEquals($this->amqpMessage->getBody(), $this->convertedMessage->getBody());
    }

    public function testConvertToThenConsumerTagHeader()
    {
        $this->assertHeader('consumer_tag', AmqpMessageHeaders::CONSUMER_TAG);
    }

    public function testConvertToThenDeliveryTagHeader()
    {
        $this->assertHeader('delivery_tag', AmqpMessageHeaders::DELIVERY_TAG);
    }

    public function testConvertToThenRedeliveredHeader()
    {
        $this->assertHeader('redelivered', AmqpMessageHeaders::REDELIVERED);
    }

    public function testConvertToThenExchangeHeader()
    {
        $this->assertHeader('exchange', AmqpMessageHeaders::EXCHANGE);
    }

    public function testConvertToThenRoutingKeyHeader()
    {
        $this->assertHeader('routing_key', AmqpMessageHeaders::ROUTING_KEY);
    }

    public function testConvertToThenContentTypeHeader()
    {
        $this->assertHeader('content_type', AmqpMessageHeaders::CONTENT_TYPE);
    }

    public function testConvertToThenContentEncodingHeader()
    {
        $this->assertHeader('content_encoding', AmqpMessageHeaders::CONTENT_ENCODING);
    }

    public function testConvertToThenReplyToHeader()
    {
        $this->assertHeader('reply_to', AmqpMessageHeaders::REPLY_TO);
    }

    public function testConvertToThenCorrelationIdHeader()
    {
        $this->assertHeader('correlation_id', AmqpMessageHeaders::CORRELATION_ID);
    }

    public function testConvertToThenPriorityHeader()
    {
        $this->assertHeader('priority', AmqpMessageHeaders::PRIORITY);
    }

    private function assertHeader($property, $header)
    {
        $current = $this->convertedMessage->getHeader($header);
        $this->assertEquals($this->amqpMessage->get($property), $current);
    }

    public function testConvertToThenAmqpMessageBody()
    {
        $message = $this->converter->convertTo($this->converter->convertFrom($this->amqpMessage));
        $this->assertEquals($this->amqpMessage->getBody(), $message->getBody());
    }

    public function testConvertToThenMessageProperties()
    {
        $message = $this->converter->convertTo($this->converter->convertFrom($this->amqpMessage));
        $this->assertEquals($this->amqpMessage->get_properties(), $message->get_properties());
    }
}
