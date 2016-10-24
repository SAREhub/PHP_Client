<?php

namespace SAREhub\Client\Amqp;

use GuzzleHttp\Promise\CancellationException;
use GuzzleHttp\Promise\Promise;
use PhpAmqpLib\Channel\AMQPChannel;
use SAREhub\Client\Event\EventEnvelope;
use SAREhub\Client\Event\EventStreamSink;
use SAREhub\Client\Event\EventStreamSource;
use SAREhub\Client\Event\NullEventStreamSink;
use SAREhub\Client\Util\StreamHelper;
use SAREhub\Commons\Misc\Parameters;

/**
 * Event stream source as AMQP queue
 */
class BasicAmqpEventStreamSource implements EventStreamSource {
	
	const DEFAULT_TIMEOUT = 3;
	const DEFAULT_CONSUMER_TAG = '';
	
	/** @var AMQPChannel */
	private $channel;
	
	/** @var string */
	private $queueName;
	
	/** @var string */
	private $consumerTag;
	
	/** @var AmqpEventConsumerBuilder */
	private $consumerBuilder;
	
	/** @var \Generator */
	private $flowControl = null;
	
	/** @var EventStreamSink */
	private $sink;
	
	/**
	 * @var StreamHelper
	 */
	private $streamHelper;
	
	/**
	 * @param AMQPChannel $channel
	 * @param array $parameters {
	 *
	 *
	 * @type string $queueName Name of queue for consuming event
	 * @type string $consumerTag Optional. Id of consumer
	 * @type AmqpEventConsumerBuilder $consumerBuilder
	 * }
	 */
	public function __construct(AMQPChannel $channel, array $parameters) {
		$this->channel = $channel;
		$parameters = new Parameters($parameters);
		$this->queueName = $parameters->getRequired('queueName');
		$this->consumerTag = $parameters->get('consumerTag', self::DEFAULT_CONSUMER_TAG);
		$this->consumerBuilder = $parameters->getRequired('consumerBuilder');
		$this->consumerBuilder->source($this);
		$this->sink = new NullEventStreamSink();
		
		$this->streamHelper = new StreamHelper();
	}
	
	/**
	 * @param StreamHelper $helper
	 */
	public function withStreamHelper(StreamHelper $helper) {
		$this->streamHelper = $helper;
	}
	
	/**
	 * Returns simple process promise factory
	 * @return \Closure
	 */
	public static function createDefaultProcessPromiseFactory() {
		return function (self $source, EventEnvelope $eventEnvelope) {
			$promise = new Promise();
			$promise->then(function () use ($source, $eventEnvelope) {
				$source->getChannel()->basic_ack($eventEnvelope->getProperties()->getDeliveryTag());
			}, function ($error) use ($source, $eventEnvelope) {
				$requeue = $error instanceof CancellationException;
				$source->getChannel()->basic_reject($eventEnvelope->getProperties()->getDeliveryTag(), $requeue);
			});
			
			return $promise;
		};
	}
	
	/**
	 * @return AMQPChannel
	 */
	public function getChannel() {
		return $this->channel;
	}
	
	/**
	 * {@inheritDoc}
	 * Nonblocking implementation based on Generator
	 * ```php
	 * $source->flow();
	 * $flowControl = $source->getFlowControl();
	 * $flowControl->next();
	 * $source->stopFlow();
	 * ```
	 */
	public function flow() {
		if (!$this->isInFlowMode()) {
			$this->getChannel()->basic_qos(null, 1, null);
			return $this->createFlowControl();
		}
		
		throw new AmqpException("EventStreamSource is in flow mode");
	}
	
	/**
	 * @return bool
	 */
	public function isInFlowMode() {
		return $this->flowControl !== null;
	}
	
	private function createFlowControl() {
		$flowControlBuilder = function () {
			yield true;
			$channel = $this->getChannel();
			$consumer = $this->consumerBuilder->build();
			$channel->basic_consume($this->getQueue(), $this->getConsumerTag(), false, false, false, false, $consumer);
			while (count($channel->callbacks)) {
				if (!$this->isInFlowMode()) {
					break;
				}
				
				$socket = $channel->getConnection()->getSocket();
				$changeStreamsCount = $this->streamHelper->select($socket, self::DEFAULT_TIMEOUT);
				if ($changeStreamsCount > 0) {
					$channel->wait(null, true, self::DEFAULT_TIMEOUT);
				}
				yield true;
			}
		};
		$this->flowControl = $flowControlBuilder();
	}
	
	/**
	 * @return string
	 */
	public function getQueue() {
		return $this->queueName;
	}
	
	/**
	 * @return string
	 */
	public function getConsumerTag() {
		return $this->consumerTag;
	}
	
	/**
	 * @return \Generator
	 */
	public function getFlowControl() {
		return $this->flowControl;
	}
	
	/**
	 * Stops flow mode in source
	 * @throws AmqpException When source isn't in flow mode
	 */
	public function stopFlow() {
		if ($this->isInFlowMode()) {
			$this->channel->basic_cancel($this->getConsumerTag());
			$this->flowControl = null;
			
		}
	}
	
	public function pipe(EventStreamSink $sink) {
		$this->unpipe();
		$this->sink = $sink;
		$this->sink->onPipe($this);
	}
	
	public function unpipe() {
		$this->sink->onUnpipe($this);
		$this->sink = new NullEventStreamSink();
	}
	
	public function getSink() {
		return $this->sink;
	}
}