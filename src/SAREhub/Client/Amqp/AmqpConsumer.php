<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Message\Message;
use SAREhub\Client\Processor\Processor;
use SAREhub\Component\Worker\Service\ServiceSupport;

class AmqpConsumer extends ServiceSupport {
	
	/**
	 * @var AMQPChannel
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
	private $waitTimeout = 3;
	
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
	 * @param AMQPChannel $channel
	 * @return $this
	 */
	public function withChannel(AMQPChannel $channel) {
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
		$this->getChannel()->basic_consume(
		  $this->getQueueName(),
		  $this->getConsumerTag(),
		  false,
		  false,
		  false,
		  false,
		  [$this, 'consume']);
	}
	
	public function consume(AMQPMessage $in) {
		$exchange = $this->createExchange($in);
		$this->getNextProcessor()->process($exchange);
		$this->confirmProcess($exchange);
	}
	
	/**
	 * @param AMQPMessage $in
	 * @return Message
	 */
	private function createExchange(AMQPMessage $in) {
		return BasicExchange::withIn($this->converter->convertFrom($in));
	}
	
	private function confirmProcess(Exchange $exchange) {
		$deliveryTag = $exchange->getIn()->getHeader(AmqpMessageHeaders::DELIVERY_TAG);
		if ($exchange->isFailed()) {
			$this->getChannel()->basic_nack($deliveryTag, false, true);
		} else {
			$this->getChannel()->basic_ack($deliveryTag);
		}
	}
	
	protected function doTick() {
		if (count($this->getChannel()->callbacks)) {
			try {
				$this->getChannel()->wait(null, true, $this->getWaitTimeout());
			} catch (AMQPTimeoutException $e) {
				sleep(1); // when queue is empty we can wait some time
			}
		}
	}
	
	protected function doStop() {
		$this->channel->basic_cancel($this->getConsumerTag());
	}
	
	/**
	 * @return AMQPChannel
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
	 * @return int
	 */
	public function getWaitTimeout() {
		return $this->waitTimeout;
	}
	
	/**
	 * @return Processor
	 */
	public function getNextProcessor() {
		return $this->nextProcessor;
	}
}