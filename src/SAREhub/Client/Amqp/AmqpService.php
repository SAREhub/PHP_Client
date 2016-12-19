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
	
	public function __construct(AmqpConnectionService $connectionService) {
		$this->connectionService = $connectionService;
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
	 * @param string $queueName
	 * @param Processor $processor
	 * @return AmqpConsumer
	 */
	public function createConsumer($queueName, Processor $processor) {
		return AmqpConsumer::newInstance()
		  ->withChannel($this->createChannel())
		  ->withConverter(new AmqpMessageConverter())
		  ->withQueueName($queueName)
		  ->withProcessor($processor);
	}
	
	/**
	 * @return AmqpProducer
	 */
	public function createProducer() {
		return AmqpProducer::newInstance()
		  ->withChannel($this->createChannel())
		  ->withConverter(new AmqpMessageConverter());
	}
	
	/**
	 * @return AmqpChannelWrapper
	 */
	private function createChannel() {
		return $this->connectionService->createChannel();
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