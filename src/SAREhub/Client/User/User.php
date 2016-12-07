<?php

namespace SAREhub\Client\User;

/**
 * Base user class
 *
 */
class User {
	
	/**
	 * @var array
	 */
	private $keys;
	
	/**
	 * @param array $keys
	 */
	public function __construct(array $keys) {
		$this->keys = $keys;
	}
	
	/**
	 * @param string $type
	 * @return string|null
	 */
	public function getKey($type) {
		return $this->hasKey($type) ? $this->keys[$type] : null;
	}
	
	public function hasKey($type) {
		return isset($this->keys[$type]);
	}
	
	/**
	 * @return string[]
	 */
	public function getKeys() {
		return $this->keys;
	}
}