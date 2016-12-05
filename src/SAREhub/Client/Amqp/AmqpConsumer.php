<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\Processor;
use SAREhub\Component\Worker\Service\ServiceSupport;

class AmqpConsumer extends ServiceSupport {
	
	/**
	 * @var AmqpChannelWrapper
	 */
	private $channel;
	
	/**
	 * @var string
	 */
	private $queueName;
	
	/**
	 * @var string
	 */
	private $consumerTag = '';
	
	/**
	 * @var AmqpMessageConverter
	 */
	private $converter;
	
	/**
	 * @var Processor
	 */
	private $nextProcessor;
	
	
	/**
	 * @return AmqpConsumer
	 */
	public static function newInstance() {
		return new self();
	}
	
	/**
	 * @param AmqpChannelWrapper
	 * @return $this
	 */
	public function withChannel(AmqpChannelWrapper $channel) {
		$this->channel = $channel;
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return $this
	 */
	public function withQueueName($name) {
		$this->queueName = $name;
		return $this;
	}
	
	/**
	 * @param string $tag
	 * @return $this
	 */
	public function withConsumerTag($tag) {
		$this->consumerTag = $tag;
		return $this;
	}
	
	/**
	 * @param AmqpMessageConverter $converter
	 * @return $this
	 */
	public function withConverter(AmqpMessageConverter $converter) {
		$this->converter = $converter;
		return $this;
	}
	
	/**
	 * @param Processor $processor
	 * @return $this
	 */
	public function withNextProcessor(Processor $processor) {
		$this->nextProcessor = $processor;
		return $this;
	}
	
	protected function doStart() {
		$this->getChannel()->registerConsumer($this);
	}
	
	public function consume(AMQPMessage $in) {
		$exchange = $this->createExchange($in);
		$this->getNextProcessor()->process($exchange);
		$this->confirmProcess($exchange);
	}
	
	/**
	 * @param AMQPMessage $in
	 * @return Exchange
	 */
	private function createExchange(AMQPMessage $in) {
		return BasicExchange::withIn($this->converter->convertFrom($in));
	}
	
	private function confirmProcess(Exchange $exchange) {
		$deliveryTag = $exchange->getIn()->getHeader(AmqpMessageHeaders::DELIVERY_TAG);
		if ($exchange->isFailed()) {
			$this->getChannel()->nack($deliveryTag);
		} else {
			$this->getChannel()->ack($deliveryTag);
		}
	}
	
	protected function doTick() {
		$this->getChannel()->wait();
	}
	
	protected function doStop() {
		$this->channel->cancelConsume();
	}
	
	/**
	 * @return AmqpChannelWrapper
	 */
	public function getChannel() {
		return $this->channel;
	}
	
	/**
	 * @return string
	 */
	public function getQueueName() {
		return $this->queueName;
	}
	
	/**
	 * @return string
	 */
	public function getConsumerTag() {
		return $this->consumerTag;
	}
	
	/**
	 * @return Processor
	 */
	public function getNextProcessor() {
		return $this->nextProcessor;
	}
}