<?php

namespace SAREhub\Client\Event;


use SAREhub\Client\User\User;

class BasicEvent implements Event {
	
	private $eventType;
	private $time;
	private $user;
	private $properties = [];
	
	protected function __construct($eventType) {
		$this->eventType = $eventType;
	}
	
	/**
	 * @param $eventType
	 * @return BasicEvent
	 */
	public static function newInstanceOf($eventType) {
		return new self($eventType);
	}
	
	/**
	 * @param int $time
	 * @return $this
	 */
	public function withTime($time) {
		$this->time = $time;
		return $this;
	}
	
	public function withUser(User $user) {
		$this->user = $user;
	}
	
	/**
	 * @param array $properties
	 * @return $this
	 */
	public function withProperties(array $properties) {
		$this->properties = $properties;
		return $this;
	}
	
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return $this
	 */
	public function withProperty($name, $value) {
		$this->properties[$name] = $value;
		return $this;
	}
	
	public function getEventType() {
		return $this->eventType;
	}
	
	public function getTime() {
		return $this->time;
	}
	
	public function getUser() {
		return $this->user;
	}
	
	public function hasUser() {
		return $this->user !== null;
	}
	
	public function getProperties() {
		return $this->properties;
	}
	
	public function getProperty($name) {
		if ($this->hasProperty($name)) {
			return $this->properties[$name];
		}
		
		throw new EventPropertyNotFoundException($this, $name);
	}
	
	public function hasProperty($name) {
		return isset($this->properties[$name]);
	}
}