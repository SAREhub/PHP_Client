<?php

namespace SAREhub\Client\User;

class CookieUserKey extends UserKey {
	
	/** @var string */
	private $cookie;
	
	/**
	 * @param string $cookie
	 */
	public function __construct($cookie) {
		$this->cookie = $cookie;
	}
	
	/**
	 * @return string
	 */
	public function getCookie() {
		return $this->cookie;
	}
	
	
	public function getKeyType() {
		return 'cookie';
	}
}