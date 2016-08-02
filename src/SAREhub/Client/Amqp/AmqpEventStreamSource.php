<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Event\EventSerializationService;
use SAREhub\Client\Event\EventStreamSource;

/**
 * Event stream source as AMQP queue
 */
class AmqpEventStreamSource extends EventStreamSource {
	
	const DEFAULT_CONSUMER_TAG = '';
	
	/** @var \PhpAmqpLib\Channel\AMQPChannel */
	private $channel;
	
	/** @var string */
	private $queueName;
	
	/** @var bool */
	private $inFlowMode = false;
	
	/** @var EventSerializationService */
	private $serialization;
	
	/**
	 * @param AMQPChannel $channel
	 * @param string $queueName
	 * @param EventSerializationService $serialization
	 */
	public function __construct(AMQPChannel $channel, $queueName, EventSerializationService $serialization) {
		$this->channel = $channel;
		$this->queueName = $queueName;
		$this->serialization = $serialization;
	}
	
	/**
	 * {@inheritDoc}
	 * Non blocking implementation based on Generator. To cancel consume send boolean "true"
	 * @return \Closure
	 */
	public function flow() {
		if (!$this->inFlowMode) {
			$this->inFlowMode = true;
			
			return function () {
				$this->channel->basic_consume($this->queueName, self::DEFAULT_CONSUMER_TAG,
				  false, false, false, false,
				  $this->createConsumeCallback());
				
				while (count($this->channel->callbacks)) {
					if ($breakFlag = (yield true)) {
						$this->channel->basic_cancel(self::DEFAULT_CONSUMER_TAG);
						$this->inFlowMode = false;
						break;
					}
					
					$this->channel->wait();
				}
			};
		}
	}
	
	public function createConsumeCallback() {
		$sink = $this->getSink();
		return function (AMQPMessage $message) use ($sink) {
			$message->get();
		};
	}
	
	public function read() {
		// @TODO implement
	}
	
	/**
	 * @return string
	 */
	public function getQueue() {
		return $this->queueName;
	}
}