<?php

namespace SAREhub\Client\Event;

use SAREhub\Client\User\User;

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
	 * @return User
	 */
	public function getUser();
	
	/**
	 * @return bool
	 */
	public function hasUser();
	
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