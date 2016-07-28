<?php

namespace SAREhub\Client\Event\User;

use SAREhub\Client\Event\Event;

/**
 * Base user event class
 */
abstract class UserEvent extends Event {
	
	/** @var User */
	private $user;
	
	/**
	 * @param User $user
	 */
	public function __construct(User $user) {
		$this->user = $user;
	}
	
	/**
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}
}