<?php

namespace SAREhub\Client\User;

class MobileUserKey extends UserKey {
	
	/** @var string */
	private $mobile;
	
	/**
	 * @param string $mobile
	 */
	public function __construct($mobile) {
		$this->mobile = $mobile;
	}
	
	/**
	 * @return string
	 */
	public function getMobile() {
		return $this->mobile;
	}
	
	public function getKeyType() {
		return 'mobile';
	}
}