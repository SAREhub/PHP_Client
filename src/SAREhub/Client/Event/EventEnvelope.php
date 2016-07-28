<?php

namespace SAREhub\Client\Event;
use SAREhub\Commons\Misc\Parameters;

/**
 * Container for event with process state change notification callbacks
 */
class EventEnvelope {
	
	private $event;
	
	private $processed = false;
	private $processedCallback;
	
	private $cancelled = false;
	private $cancelledCallback;
	
	private $properties;
	
	/**
	 * Creates envelope with default noops callbacks
	 * @param Event $event
	 */
	public function __construct(Event $event) {
		$this->event = $event;
		$this->processedCallback = $cancelledCallback = function () { };
	}
	
	/**
	 * @return Event
	 */
	public function getEvent() {
		return $this->event;
	}
	
	/**
	 * Execute once registered processed callback and sets that envelope as processed
	 */
	public function processed() {
		if (!$this->isProcessedOrCancelled()) {
			($c = $this->processedCallback) && $c($this);
			$this->processed = true;
		}
	}
	
	/**
	 * @return bool
	 */
	public function isProcessedOrCancelled() {
		return $this->isProcessed() || $this->isCancelled();
	}
	
	/**
	 * @return bool
	 */
	public function isProcessed() {
		return $this->processed;
	}
	
	/**
	 * @return bool
	 */
	public function isCancelled() {
		return $this->cancelled;
	}
	
	/**
	 * @param callable $callback
	 */
	public function setProcessedCallback(callable $callback) {
		$this->processedCallback = $callback;
	}
	
	/**
	 * Execute once registered cancelled callback and sets that envelope as cancelled
	 */
	public function cancelled() {
		if (!$this->isProcessedOrCancelled()) {
			($c = $this->cancelledCallback) && $c($this);
			$this->cancelled = true;
		}
	}
	
	/**
	 * @param callable $callback
	 */
	public function setCancelledCallback(callable $callback) {
		$this->cancelledCallback = $callback;
	}
	
	/**
	 * @return Parameters
	 */
	public function getProperties() {
		return $this->properties;
	}
	
	/**
	 * @param array $properties
	 */
	public function setProperties($properties) {
		$this->properties = $properties;
	}
	
}