<?php

namespace SAREhub\Client\Amqp;


/**
 * Routing key is string in format: part1[.partN]
 */
class RoutingKey implements \IteratorAggregate {
	
	/** @var array */
	protected $parts;
	
	public function __construct(array $parts = null) {
		$this->parts = $parts ? $parts : [];
	}
	
	/**
	 * @param string $routingKeyString
	 * @return RoutingKey
	 */
	public static function createFromString($routingKeyString) {
		return new self(explode('.', $routingKeyString));
	}
	
	/**
	 * @param string part
	 * @return $this
	 */
	public function addPart($part) {
		$this->parts[] = $part;
		return $this;
	}
	
	/**
	 * @param int index
	 * @return string
	 */
	public function getPart($index) {
		return isset($this->parts[$index]) ? $this->parts[$index] : '';
	}
	
	/**
	 * @return bool
	 */
	public function isEmpty() {
		return empty($this->parts);
	}
	
	public function getIterator() {
		return $this->parts;
	}
	
	/**
	 * @return array
	 */
	public function getParts() {
		return $this->parts;
	}
	
	public function __toString() {
		return implode('.', $this->parts);
	}
	
}