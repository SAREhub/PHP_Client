<?php

namespace SAREhub\Client\Event;

/**
 * Base event class
 */
interface Event {
	
	/**
	 * @return int
	 */
	public function getTime();
	
	/**
	 * Returns event type name
	 * @return string
	 */
	public function getEventType();
	
	public function getAttribute($name);
	
	public function hasAttribute($name);
	
	/**
	 * @return array
	 */
	public function getAttributes();
}