<?php

namespace SAREhub\Client\Event;

/**
 * Base event class
 */
abstract class Event {
	
	/**
	 * Returns event type name
	 * @return string
	 */
	public abstract function getEventType();
}