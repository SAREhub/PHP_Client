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
	 * Finds user key by type
	 * @param string $keyType = $type;
	 * @return UserKey|null
	 */
	public function findKeyByType($keyType) {
		foreach ($this->keys as $key) {
			if ($key->getKeyType() === $keyType) {
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