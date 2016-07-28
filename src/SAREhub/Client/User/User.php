<?php

namespace SAREhub\Client\User;

/**
 * Base user class
 *
 */
class User {
	
	/** @var UserKey[] */
	private $keys;
	
	/**
	 * @param UserKey[] $keys
	 */
	public function __construct(array $keys) {
		$this->keys = $keys;
	}
	
	/**
	 * Finds user key by class name
	 * @param string $userKeyClass User key class name
	 * @return UserKey|null
	 */
	public function findKeyByClass($userKeyClass) {
		foreach ($this->keys as $key) {
			if (is_a($key, $userKeyClass)) {
				return $key;
			}
		}
		return null;
	}
	
	/**
	 * @return UserKey[]
	 */
	public function getKeys() {
		return $this->keys;
	}
}