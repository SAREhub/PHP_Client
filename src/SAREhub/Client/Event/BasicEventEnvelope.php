<?php

namespace SAREhub\Client\Event;

use GuzzleHttp\Promise\Promise;

class BasicEventEnvelope implements EventEnvelope {
	
	/** @var Event */
	private $event;
	
	/** @var  null|Promise */
	private $processPromise;
	
	/** @var null|EventEnvelopeProperties */
	private $properties;
	
	/**
	 * @param Event $event
	 */
	public function __construct(Event $event) {
		$this->event = $event;
	}
	
	public function getEvent() {
		return $this->event;
	}
	
	public function markAsProcessed() {
		$this->hasProcessPromise() && $this->processPromise->resolve($this);
	}
	
	public function hasProcessPromise() {
		return (bool)$this->processPromise;
	}
	
	public function markAsCancelled() {
		$this->hasProcessPromise() && $this->processPromise->cancel();
	}
	
	public function markAsProcessedExceptionally(\Exception $e) {
		$this->hasProcessPromise() && $this->processPromise->reject($e);
	}
	
	public function getProcessPromise() {
		return $this->processPromise;
	}
	
	public function setProcessPromise(Promise $processPromise) {
		$this->processPromise = $processPromise;
	}
	
	public function getProperties() {
		return $this->properties;
	}
	
	public function setProperties(EventEnvelopeProperties $properties) {
		$this->properties = $properties;
	}
	
	public function hasProperties() {
		return (bool)$this->properties;
	}
}