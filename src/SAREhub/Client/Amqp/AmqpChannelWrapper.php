<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpChannelWrapper {
	
	/**
	 * @var AmqpConnectionService
	 */
	private $connectionService;
	
	/**
	 * @var AMQPChannel
	 */
	private $channel;
	
	/**
	 * @var int
	 */
	private $waitTimeout = 3;
	
	/**
	 * @var AmqpConsumer
	 */
	private $consumer;
	
	public function __construct(AMQPChannel $channel, AmqpConnectionService $service) {
		$this->channel = $channel;
		$this->connectionService = $service;
	}
	
	public function registerConsumer(AmqpConsumer $consumer) {
		$this->consumer = $consumer;
		$this->getChannel()->basic_consume(
		  $consumer->getQueueName(),
		  $consumer->getConsumerTag(),
		  false,
		  false,
		  false,
		  false,
		  [$consumer, 'consume']);
	}
	
	public function cancelConsume() {
		$this->channel->basic_cancel($this->consumer->getConsumerTag());
		$this->consumer = null;
	}
	
	public function wait() {
		if ($this->hasConsumer()) {
			try {
				$this->getChannel()->wait(null, true, $this->getWaitTimeout());
			} catch (AMQPTimeoutException $e) {
				sleep(1); // when queue is empty we can wait some time
			}
		}
	}
	
	public function ack($deliveryTag) {
		$this->getChannel()->basic_ack($deliveryTag, false);
	}
	
	public function nack($deliveryTag) {
		$this->getChannel()->basic_nack($deliveryTag, false, true);
	}
	
	public function publish(AMQPMessage $message, $exchange, RoutingKey $routingKey) {
		$this->channel->basic_publish($message, $exchange, (string)$routingKey);
	}
	
	/**
	 * @return AMQPChannel
	 */
	public function getChannel() {
		return $this->channel;
	}
	
	/**
	 * @return int
	 */
	public function getWaitTimeout() {
		return $this->waitTimeout;
	}
	
	/**
	 * @return AmqpConsumer
	 */
	public function getConsumer() {
		return $this->consumer;
	}
	
	/**
	 * @return bool
	 */
	public function hasConsumer() {
		return $this->consumer !== null;
	}
}