<?php

namespace SAREhub\Client\User;

class BasicUserKey implements UserKey {
	
	private $keyType;
	private $value;
	
	public function __construct($keyType, $value) {
		$this->keyType = $keyType;
		$this->value = $value;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getKeyType() {
		return $this->keyType;
	}
}