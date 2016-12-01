<?php

namespace unit\SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use SAREhub\Client\Amqp\AmqpConsumer;
use SAREhub\Client\Amqp\AmqpMessageConverter;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\Processor;

class AmqpEventConsumerTest extends TestCase {
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $channel;
	
	/**
	 * @var AmqpMessageConverter | PHPUnit_Framework_MockObject_MockObject
	 */
	private $converter;
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $processor;
	
	/**
	 * @var AmqpConsumer
	 */
	private $consumer;
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $amqpMessage;
	
	protected function setUp() {
		$this->channel = $this->createMock(AMQPChannel::class);
		$this->processor = $this->createMock(Processor::class);
		
		$this->converter = $this->createMock(AmqpMessageConverter::class);
		
		$this->consumer = AmqpConsumer::newInstance()
		  ->withChannel($this->channel)
		  ->withQueueName('test')
		  ->withConverter($this->converter)
		  ->withNextProcessor($this->processor);
		
		$this->amqpMessage = $this->createMock(AMQPMessage::class);
	}
	
	public function testStartThenBasicConsume() {
		$this->channel->expects($this->once())
		  ->method('basic_consume')
		  ->with('test', '', false, false, false, false, [$this->consumer, 'consume']);
		$this->consumer->start();
	}
	
	public function testTickThenCallChannelWait() {
		$this->channel->callbacks = ['test'];
		$this->channel->expects($this->once())
		  ->method('wait')
		  ->with(null, true, 3);
		
		$this->consumer->start();
		$this->consumer->tick();
	}
	
	public function testTickWhenTimeoutExceptionThenSilent() {
		$this->channel->callbacks = ['test'];
		$this->channel->method('wait')->willThrowException(new AMQPTimeoutException());
		$this->consumer->start();
		$this->consumer->tick();
	}
	
	public function testTickWhenNoCallbacksThenNotCallChannelWait() {
		$this->channel->expects($this->never())->method('wait');
		$this->consumer->start();
		$this->consumer->tick();
	}
	
	public function testStopThenCallChannelBasicCancel() {
		$this->channel->expects($this->once())
		  ->method('basic_cancel')
		  ->with('', false, false);
		$this->consumer->start();
		$this->consumer->stop();
	}
	
	public function testConsumeThenConvertMessage() {
		$this->converter->expects($this->once())
		  ->method('convert')
		  ->with($this->identicalTo($this->amqpMessage))
		  ->willReturn(BasicMessage::withBody('test'));
		
		$this->consumer->consume($this->amqpMessage);
	}
	
	public function testConsumeThenProcessorProcess() {
		$message = BasicMessage::withBody('test');
		$this->converter->method('convert')->willReturn($message);
		$this->processor->expects($this->once())
		  ->method('process')->with($this->callback(function (Exchange $exchange) use ($message) {
			  return $exchange->getIn() === $message;
		  }));
		$this->consumer->consume($this->amqpMessage);
	}
}
