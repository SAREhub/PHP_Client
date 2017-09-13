<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class AmqpChannelWrapperTest extends TestCase {
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $channel;
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $service;
	
	/**
	 * @var AmqpChannelWrapper
	 */
	private $wrapper;
	
	/**
	 * @var AmqpConsumer
	 */
	private $consumer;
	
	protected function setUp() {
		$this->channel = $this->createMock(AMQPChannel::class);
		$this->service = $this->createMock(AmqpService::class);
		
		$this->wrapper = new AmqpChannelWrapper($this->channel, $this->service);
		$this->consumer = $this->createMock(AmqpConsumer::class);
		$this->consumer->method('getQueueName')->willReturn('queue');
		$this->consumer->method('getConsumerTag')->willReturn('tag');
		$this->consumer->method('getPrefetchCount')->willReturn(1);
	}
	
	public function testRegisterConsumerThenChannelConsume() {
		$this->channel->expects($this->once())->method('basic_qos')->with(0, 1, false);
		$this->channel->expects($this->once())->method('basic_consume')
		  ->with('queue', 'tag', false, false, false, false, [$this->consumer, 'consume']);
		$this->wrapper->registerConsumer($this->consumer);
	}
	
	public function testRegisterConsumerThenConsumer() {
		$this->wrapper->registerConsumer($this->consumer);
		$this->assertSame($this->consumer, $this->wrapper->getConsumer());
	}
	
	public function testCancelConsumeThenChannelBasicCancel() {
		$this->wrapper->registerConsumer($this->consumer);
		$this->channel->expects($this->once())
		  ->method('basic_cancel')
		  ->with($this->consumer->getConsumerTag(), false, true);
		$this->wrapper->cancelConsume();
	}
	
	public function testCancelConsumeThenEmptyConsumer() {
		$this->wrapper->registerConsumer($this->consumer);
		$this->wrapper->cancelConsume();
		$this->assertNull($this->wrapper->getConsumer());
	}
	
	public function testWaitWhenConsumerThenChannelWait() {
		$this->wrapper->registerConsumer($this->consumer);
		//$this->channel->expects($this->once())->method('wait')->with(null, true, $this->wrapper->getWaitTimeout());
		$this->channel->expects($this->once())->method('wait')
		  ->with(null, $this->callback(function ($c) {
			  return $c;
		  }), $this->wrapper->getWaitTimeout());
		$this->wrapper->wait();
	}
	
	public function testWaitWhenConsumerAndTimeoutThenSilent() {
		$this->wrapper->registerConsumer($this->consumer);
		$this->assertTrue(true);
		$this->channel->method('wait')->willThrowException(new AMQPTimeoutException("Timeout waiting on channel"));
		$this->wrapper->wait();
	}

    public function testWaitWhenConsumerAndTimeoutAndOtherTimeoutInfoThenRethrow() {
        $this->wrapper->registerConsumer($this->consumer);
        $exception = new AMQPTimeoutException("other timeout message");
        $this->expectException(AMQPTimeoutException::class);
        $this->channel->method('wait')->willThrowException($exception);
        $this->wrapper->wait();
    }
	
	public function testAckThenChannelAck() {
		$this->channel->expects($this->once())->method('basic_ack')->with(1, false);
		$this->wrapper->ack(1);
	}
	
	public function testNackThenChannelNack() {
		$this->channel->expects($this->once())->method('basic_nack')->with(1, false, true);
		$this->wrapper->nack(1);
	}
	
	public function testPublishThenChannelBasicPublish() {
		$amqpMessage = new AMQPMessage();
		$routingKey = new RoutingKey('part1.part2');
		$this->channel->expects($this->once())
		  ->method('basic_publish')
		  ->with($amqpMessage, 'exchange', (string)$routingKey);
		
		$this->wrapper->publish($amqpMessage, 'exchange', $routingKey);
	}
}
