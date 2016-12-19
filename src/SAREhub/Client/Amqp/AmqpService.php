<?php

namespace SAREhub\Client\Amqp;

use SAREhub\Client\Processor\Processor;
use SAREhub\Component\Worker\Service\ServiceSupport;

class AmqpService extends ServiceSupport {
	
	/**
	 * @var AmqpChannelFactory
	 */
	private $channelFactory;
	
	/**
	 * @var AmqpConsumer
	 */
	private $consumer;
	
	/**
	 * @var AmqpProducer
	 */
	private $producer;
	
	/**
	 * @var AmqpMessageConverter
	 */
	private $amqpMessageConverter;
	
	public function __construct(AmqpChannelFactory $channelFactory) {
		$this->channelFactory = $channelFactory;
		$this->amqpMessageConverter = new AmqpMessageConverter();
	}
	
	/**
	 * @param $queueName
	 * @param Processor $processor
	 * @return AmqpConsumer
	 */
	public function createConsumer($queueName, Processor $processor) {
		return AmqpConsumer::newInstance()
		  ->withChannel($this->channelFactory->create())
		  ->withConverter($this->amqpMessageConverter)
		  ->withQueueName($queueName)
		  ->withProcessor($processor);
	}
	
	/**
	 * @return AmqpProducer
	 */
	public function createProducer() {
		return AmqpProducer::newInstance()
		  ->withChannel($this->channelFactory->create())
		  ->withConverter($this->amqpMessageConverter);
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
	 * @return AmqpConsumer
	 */
	public function getConsumer() {
		return $this->consumer;
	}
	
	/**
	 * @param AmqpConsumer $consumer
	 */
	public function setConsumer(AmqpConsumer $consumer) {
		$this->consumer = $consumer;
	}
	
	/**
	 * @return AmqpProducer
	 */
	public function getProducer() {
		return $this->producer;
	}
	
	/**
	 * @param AmqpProducer $producer
	 */
	public function setProducer(AmqpProducer $producer) {
		$this->producer = $producer;
	}
}