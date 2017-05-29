<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\Processor;
use SAREhub\Commons\Service\ServiceSupport;

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
	 * @var int
	 */
	private $prefetchCount = 1;
	
	/**
	 * @var AmqpMessageConverter
	 */
	private $converter;
	
	/**
	 * @var Processor
	 */
	private $processor;
	
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
	
	public function withPrefetchCount(int $count) {
		$this->prefetchCount = $count;
		return $this;
	}
	
	/**
	 * @param Processor $processor
	 * @return $this
	 */
	public function withProcessor(Processor $processor) {
		$this->processor = $processor;
		return $this;
	}
	
	protected function doStart() {
		$this->getChannel()->registerConsumer($this);
	}
	
	public function consume(AMQPMessage $in) {
		$exchange = $this->createExchange($in);
		$orginalIn = $exchange->getIn()->copy();
		
		$this->getLogger()->debug('got message', ['message' => $orginalIn]);
		$this->getProcessor()->process($exchange);
		$this->confirmProcess($exchange, $in->get('delivery_tag'));
	}
	
	/**
	 * @param AMQPMessage $in
	 * @return Exchange
	 */
	private function createExchange(AMQPMessage $in) {
		return BasicExchange::newInstance()->setIn($this->convertMessage($in));
	}
	
	private function convertMessage(AMQPMessage $in) {
		return $this->converter->convertFrom($in);
	}
	
	private function confirmProcess(Exchange $exchange, $deliveryTag) {
		if ($exchange->isFailed()) {
			$this->getLogger()->debug('process failed ', ['exchange' => $exchange]);
			$this->getChannel()->nack($deliveryTag);
		} else {
			$this->getLogger()->debug('process success ', ['exchange' => $exchange]);
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
	
	public function getPrefetchCount() {
		return $this->prefetchCount;
	}
	
	/**
	 * @return Processor
	 */
	public function getProcessor() {
		return $this->processor;
	}
}