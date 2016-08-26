<?php

namespace SAREhub\Client\User;

/**
 * User key type for SAREhub user profile id
 * That id is 12 bytes number represents in hex string
 */
class HubProfileIdUserKey extends UserKey {
	
	/** @var string */
	private $id;
	
	/**
	 * @param string $id
	 */
	public function __construct($id) {
		$this->id = $id;
	}
	
	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
	
	public function getKeyType() {
		return 'id';
	}
}