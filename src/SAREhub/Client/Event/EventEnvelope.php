<?php

namespace SAREhub\Client\Event;

use GuzzleHttp\Promise\Promise;

/**
 * Container for event with processPromise
 */
interface EventEnvelope {
	
	/**
	 * @return Event
	 */
	public function getEvent();
	
	/**
	 * Marks envelope as processed, it can call promise.resolve
	 */
	public function markAsProcessed();
	
	/**
	 * Marks envelope as process cancelled, it can call promise.cancel
	 */
	public function markAsCancelled();
	
	/**
	 * Marks envelope as processed with some error, it can call promise.reject
	 * @param \Exception $e
	 */
	public function markAsProcessedExceptionally(\Exception $e);
	
	/**
	 * @return Promise|null
	 */
	public function getProcessPromise();
	
	/**
	 * @return bool
	 */
	public function hasProcessPromise();
	
	/**
	 * @param Promise $processPromise
	 * @return mixed
	 */
	public function setProcessPromise(Promise $processPromise);
	
	/*
	 * @return EventEnvelopeProperties
	 */
	public function getProperties();
	
	/*
	 * @return bool
	 */
	public function hasProperties();
	
	/**
	 * @param EventEnvelopeProperties $properties
	 */
	public function setProperties(EventEnvelopeProperties $properties);
}