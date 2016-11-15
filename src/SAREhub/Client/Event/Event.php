<?php

namespace SAREhub\Client\Event;

/**
 * Base event class
 */
interface Event {
	
	/**
	 * Returns event type name
	 * @return string
	 */
	public function getEventType();
	
	/**
	 * @return int
	 */
	public function getTime();
	
	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getAttribute($name);
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAttribute($name);
	
	/**
	 * @return array
	 */
	public function getAttributes();
}