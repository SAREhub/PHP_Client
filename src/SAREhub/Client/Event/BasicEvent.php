<?php

namespace SAREhub\Client\Event;


class BasicEvent implements Event {
	
	private $eventType;
	private $time;
	private $attributes = [];
	
	public function __construct($eventType) {
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
	
	/**
	 * @param array $attributes
	 * @return $this
	 */
	public function withAttributes(array $attributes) {
		$this->attributes = $attributes;
		return $this;
	}
	
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return $this
	 */
	public function withAttribute($name, $value) {
		$this->attributes[$name] = $value;
		return $this;
	}
	
	public function getEventType() {
		return $this->eventType;
	}
	
	public function getTime() {
		return $this->time;
	}
	
	public function getAttributes() {
		return $this->attributes;
	}
	
	public function getAttribute($name) {
		if ($this->hasAttribute($name)) {
			return $this->attributes[$name];
		}
		
		throw new EventAttributeNotFoundException($this, $name);
	}
	
	public function hasAttribute($name) {
		return isset($this->attributes[$name]);
	}
}