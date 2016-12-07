<?php


namespace SAREhub\Client\User;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
	
	public function testGetKeyWhenFoundThenReturnKey() {
		$user = new User(['t' => 'value']);
		$this->assertSame('value', $user->getKey('t'));
	}
	
	public function testGetKeyWhenNotFoundThenReturnNull() {
		$user = new User(['t' => 'value']);
		$this->assertNull($user->getKey('t2'));
	}
}
