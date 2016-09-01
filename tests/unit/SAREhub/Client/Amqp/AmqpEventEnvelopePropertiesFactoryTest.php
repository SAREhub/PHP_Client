<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class AmqpEventEnvelopePropertiesFactoryTest extends TestCase {
	
	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $amqpMessageMock;
	
	/** @var AmqpEventEnvelopePropertiesFactory */
	private $factory;
	
	protected function setUp() {
		$this->amqpMessageMock = $this->createMock(AMQPMessage::class);
		$this->factory = new AmqpEventEnvelopePropertiesFactory();
	}
	
	public function testCreate() {
		$messageProperties = [
		  'reply_to' => 'reply',
		  'correlation_id' => 'corid',
		  'priority' => 10
		];
		
		$this->amqpMessageMock->method('get_properties')->willReturn($messageProperties);
		$this->amqpMessageMock->delivery_info = ['routing_key' => 'part1.part2'];
		
		$properties = $this->factory->createFromMessage($this->amqpMessageMock);
		$this->assertEquals('part1.part2', $properties->getRoutingKeyAsString());
		$this->assertEquals('reply', $properties->getReplyTo());
		$this->assertEquals('corid', $properties->getCorrelationId());
		$this->assertEquals(10, $properties->getPriority());
		$this->assertEquals($this->amqpMessageMock->delivery_info, $properties->getDeliveryProperties());
	}
	
}
