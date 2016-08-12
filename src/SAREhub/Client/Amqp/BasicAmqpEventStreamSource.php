<?php

namespace SAREhub\Client\Amqp;

use GuzzleHttp\Promise\CancellationException;
use GuzzleHttp\Promise\Promise;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Event\BasicEventEnvelope;
use SAREhub\Client\Event\EventEnvelope;
use SAREhub\Client\Event\EventStreamSourceBase;
use SAREhub\Commons\Misc\Parameters;

/**
 * Event stream source as AMQP queue
 */
class BasicAmqpEventStreamSource extends EventStreamSourceBase {
	
	const DEFAULT_CONSUMER_TAG = '';
	
	/** @var AMQPChannel */
	private $channel;
	
	/** @var string */
	private $queueName;
	
	/** @var string */
	private $consumerTag;
	
	/** @var callable */
	private $consumeCallback;
	
	/** @var bool */
	private $inFlowMode = false;
	
	/**
	 * @param AMQPChannel $channel
	 * @param array $parameters
	 */
	public function __construct(AMQPChannel $channel, array $parameters) {
		$this->channel = $channel;
		$parameters = new Parameters($parameters);
		$this->queueName = $parameters->getRequired('queueName');
		$this->consumerTag = $parameters->get('consumerTag', self::DEFAULT_CONSUMER_TAG);
		$consumeCallbackBuilder = $parameters->getRequired('consumeCallbackBuilder');
		$this->consumeCallback = $consumeCallbackBuilder($this);
	}
	
	public static function createConsumeCallbackBuilder(EventDeserializationService $deserializationService, $processPromiseFactory) {
		return function (self $source) use ($deserializationService, $processPromiseFactory) {
			$sink = $source->getSink();
			return function (AMQPMessage $message) use ($deserializationService, $processPromiseFactory, $sink) {
				$event = $deserializationService->deserialize($message->getBody());
				$eventEnvelopeProperties = AmqpEventEnvelopeProperties::createFromDeliveredAmqpMessage($message);
				$eventEnvelope = new BasicEventEnvelope($event, $eventEnvelopeProperties);
				$eventEnvelope->setProcessPromise($processPromiseFactory($eventEnvelope));
				$sink->write($eventEnvelope);
			};
		};
	}
	
	public static function createDefaultProcessPromiseFactory() {
		return function (BasicAmqpEventStreamSource $source, EventEnvelope $eventEnvelope) {
			/** @var AmqpEventEnvelopeProperties $properties */
			$properties = $eventEnvelope->getProperties();
			$promise = new Promise();
			$promise->then(function () use ($source, $properties) {
				$source->channel->basic_ack($properties->getDeliveryProperties()['delivery_tag']);
			}, function ($error) use ($source, $properties) {
				$requeue = $error instanceof CancellationException;
				$source->channel->basic_reject($properties->getDeliveryProperties()['delivery_tag'], $requeue);
			});
			
			return $promise;
		};
	}
	
	/**
	 * {@inheritDoc}
	 * Nonblocking implementation based on Generator. To cancel consume send boolean "true" to Generator
	 * @return \Closure
	 */
	public function flow() {
		if (!$this->inFlowMode) {
			$this->inFlowMode = true;
			$consumerTag = $this->getConsumerTag();
			$channel = $this->getChannel();
			$consumeCallback = $this->consumeCallbackBuilder($this);
			return function () use ($channel, $consumerTag, $consumeCallback) {
				$channel->basic_consume($this->queueName, $consumerTag, false, false, false, false, $consumeCallback);
				while (count($channel->callbacks)) {
					if ((yield true)) {
						$channel->basic_cancel($consumerTag);
						$this->inFlowMode = false;
						break;
					}
					
					$channel->wait();
				}
			};
		}
		
		throw new AmqpException("EventStreamSource is in flow mode");
	}
	
	/**
	 * @return string
	 */
	public function getConsumerTag() {
		return $this->consumerTag;
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
	public function getQueue() {
		return $this->queueName;
	}
}