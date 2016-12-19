<?php

namespace SAREhub\Client\Amqp;

use SAREhub\Client\Processor\Processor;
use SAREhub\Component\Worker\Service\ServiceSupport;

class AmqpService extends ServiceSupport {
	
	/**
	 * @var AmqpConnectionService
	 */
	private $connectionService;
	
	/**
	 * @var AmqpConsumer
	 */
	private $consumer;
	
	/**
	 * @var AmqpProducer
	 */
	private $producer;
	
	/**
	 * @return AmqpService
	 */
	public static function newInstance() {
		return new self();
	}
	
	/**
	 * @param AmqpConsumer $consumer
	 * @return $this
	 */
	public function withConsumer(AmqpConsumer $consumer) {
		$this->consumer = $consumer;
		return $this;
	}
	
	/**
	 * @param AmqpProducer $producer
	 * @return $this
	 */
	public function withProducer(AmqpProducer $producer) {
		$this->producer = $producer;
		return $this;
	}
	
	/**
	 * @param AmqpConnectionService $connection
	 * @param string $queueName
	 * @param Processor $processor
	 * @return AmqpConsumer
	 */
	public static function createConsumer(AmqpConnectionService $connection, $queueName, Processor $processor) {
		return AmqpConsumer::newInstance()
		  ->withChannel($connection->createChannel())
		  ->withConverter(new AmqpMessageConverter())
		  ->withQueueName($queueName)
		  ->withProcessor($processor);
	}
	
	/**
	 * @param AmqpConnectionService $connection
	 * @return AmqpProducer
	 */
	public static function createProducer(AmqpConnectionService $connection) {
		return AmqpProducer::newInstance()
		  ->withChannel($connection->createChannel())
		  ->withConverter(new AmqpMessageConverter());
	}
	
	protected function doStart() {
		$this->getConsumer()->start();
	}
	
	protected function doTick() {
		$this->getConsumer()->tick();
	}
	
	protected function doStop() {
		$this->getConsumer()->stop();
	}
	
	/**
	 * @return AmqpProducer
	 */
	public function getProducer() {
		return $this->producer;
	}
	
	/**
	 * @return AmqpConsumer
	 */
	public function getConsumer() {
		return $this->consumer;
	}
}