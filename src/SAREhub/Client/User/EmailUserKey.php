<?php

namespace SAREhub\Client\User;

class EmailUserKey extends UserKey {
	
	private $email;
	
	public function __construct($email) {
		$this->email = $email;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function getKeyType() {
		return 'email';
	}
}