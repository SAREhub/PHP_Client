<?php

namespace SAREhub\Client\Event\User;

use SAREhub\Client\Event\Event;
use SAREhub\Client\User\User;

/**
 * Base user event class
 */
abstract class UserEvent implements Event {
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @param User $user
	 */
	public function __construct(User $user = null) {
		$this->user = $user;
	}
	
	/**
	 * @param User $user
	 * @return $this
	 */
	public function withUser(User $user) {
		$this->user = $user;
		return $this;
	}
	
	/**
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}
}