<?php


namespace SAREhub\Client\User;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
	
	public function testFindKeyByTypeWhenFoundThenReturnKey() {
		$key = UserKeyFactory::email('example@example.com');
		$user = new User([$key]);
		$this->assertSame($key, $user->findKeyByType(UserKeyFactory::EMAIL_KEY_TYPE));
	}
	
	public function testFindKeyByTypeWhenNotFoundThenReturnNull() {
		$key = UserKeyFactory::email('example@example.com');
		$user = new User([$key]);
		$this->assertNull($user->findKeyByType(UserKeyFactory::COOKIE_KEY_TYPE));
	}
}
