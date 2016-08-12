<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class AmqpEventEnvelopePropertiesTest extends TestCase {
	
	public function testCreateFromDeliveredAmqpMessage() {
		$message = new AMQPMessage();
		$message->delivery_info['routing_key'] = 'routing.key';
		$properties = AmqpEventEnvelopeProperties::createFromDeliveredAmqpMessage($message);
		$this->assertEquals('routing.key', (string)$properties->getRoutingKey());
		$this->assertTrue($properties->hasDeliveryProperties());
		$this->assertEquals(['routing_key' => 'routing.key'], $properties->getDeliveryProperties());
	}
	
	public function testCreateFromDeliveredAmqpMessageWithAll() {
		$message = new AMQPMessage('', [
		  'reply_to' => 'test_to',
		  'correlation_id' => 'test_id',
		  'priority' => 1
		]);
		$message->delivery_info['routing_key'] = 'routing.key';
		$properties = AmqpEventEnvelopeProperties::createFromDeliveredAmqpMessage($message);
		
		$this->assertEquals('routing.key', (string)$properties->getRoutingKey());
		$this->assertTrue($properties->hasDeliveryProperties());
		$this->assertEquals(['routing_key' => 'routing.key'], $properties->getDeliveryProperties());
		$this->assertEquals('test_to', $properties->getReplyTo());
		$this->assertEquals('test_id', $properties->getCorrelationId());
		$this->assertEquals(1, $properties->getPriority());
	}
	
	public function testGetRoutingKeyAsString() {
		$routingKeyMock = $this->createMock(RoutingKey::class);
		$routingKeyMock->expects($this->once())->method('__toString')->willReturn('part1.part2.part3');
		$properties = new AmqpEventEnvelopeProperties();
		$properties->setRoutingKey($routingKeyMock);
		$this->assertEquals('part1.part2.part3', $properties->getRoutingKeyAsString());
	}
	
	/**
	 * @expectedException \SAREhub\Client\Amqp\EmptyRoutingKeyAmqpException
	 */
	public function testGetRoutingKeyAsStringWithoutRoutingKeySets() {
		$properties = new AmqpEventEnvelopeProperties();
		$properties->getRoutingKeyAsString();
	}
	
	public function testHasReplyTo() {
		$properties = new AmqpEventEnvelopeProperties();
		$this->assertFalse($properties->hasReplyTo());
		$properties->setReplyTo('test_to', 'test_id');
		$this->assertTrue($properties->hasReplyTo());
	}
	
	public function testToAmqpMessagePropertiesOnlyReplyTo() {
		$properties = new AmqpEventEnvelopeProperties();
		$properties->setReplyTo('test_to', 'test_id');
		$this->assertEquals([
		  'reply_to' => 'test_to',
		  'correlation_id' => 'test_id',
		], $properties->toAmqpMessageProperties());
	}
	
	public function testToAmqpMessagePropertiesOnlyResponseForReplyTo() {
		$properties = new AmqpEventEnvelopeProperties();
		$properties->setCorrelationId('test_id');
		$this->assertEquals([
		  'correlation_id' => 'test_id',
		], $properties->toAmqpMessageProperties());
	}
	
	public function testToAmqpMessagePropertiesOnlyPriority() {
		$properties = new AmqpEventEnvelopeProperties();
		$properties->setPriority(1);
		$this->assertEquals([
		  'priority' => 1
		], $properties->toAmqpMessageProperties());
	}
	
	public function testToAmqpMessagePropertiesFull() {
		$properties = new AmqpEventEnvelopeProperties();
		$properties->setReplyTo('test_to', 'test_id');
		$properties->setPriority(1);
		$this->assertEquals([
		  'reply_to' => 'test_to',
		  'correlation_id' => 'test_id',
		  'priority' => 1
		], $properties->toAmqpMessageProperties());
	}
}
