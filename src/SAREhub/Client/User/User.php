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
	public function __construct(array $keys = null) {
		$this->keys = $keys ? $keys : [];
	}
	
	/**
	 * @param string $type
	 * @return mixed|null
	 */
	public function getKey($type) {
		return $this->hasKey($type) ? $this->keys[$type] : null;
	}
	
	/**
	 * @param string $type
	 * @return bool
	 */
	public function hasKey($type) {
		return isset($this->keys[$type]);
	}
	
	/**
	 * @param string $type
	 * @param mixed $value
	 */
	public function setKey($type, $value) {
		$this->keys[$type] = $value;
	}
	
	/**
	 * @return string[]
	 */
	public function getKeys() {
		return $this->keys;
	}
}