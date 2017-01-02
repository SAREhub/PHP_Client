<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class AmqpProducerTest extends TestCase {
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $channel;
	
	/**
	 * @var AmqpMessageConverter | PHPUnit_Framework_MockObject_MockObject
	 */
	private $converter;
	
	/**
	 * @var AmqpProducer
	 */
	private $producer;
	
	protected function setUp() {
		$this->channel = $this->createMock(AmqpChannelWrapper::class);
		$this->converter = $this->createMock(AmqpMessageConverter::class);
		
		$this->producer = AmqpProducer::newInstance()
		  ->withChannel($this->channel)
		  ->withConverter($this->converter);
	}
	
	
	public function testProcessThenConverterConvertTo() {
		$message = $this->createMessage();
		$this->converter->expects($this->once())
		  ->method('convertTo')
		  ->with($message)
		  ->willReturn(new AMQPMessage());
		
		$this->producer->process(BasicExchange::withIn($message));
	}
	
	public function testProcessThenChannelPublish() {
		$message = $this->createMessage();
		$outputMessage = new AMQPMessage();
		$this->converter->method('convertTo')->willReturn($outputMessage);
		$this->channel->expects($this->once())
		  ->method('publish')
		  ->with(
			$outputMessage,
			$message->getHeader(AmqpMessageHeaders::EXCHANGE),
			$message->getHeader(AmqpMessageHeaders::ROUTING_KEY)
		  );
		
		$this->producer->process(BasicExchange::withIn($message));
	}
	
	public function createMessage() {
		$message = new BasicMessage();
		$message->setHeaders([
		  AmqpMessageHeaders::EXCHANGE => 'exchange',
		  AmqpMessageHeaders::ROUTING_KEY => new RoutingKey('part1.part2')
		]);
		
		return $message;
	}
}
