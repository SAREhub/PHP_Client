<?php


namespace SAREhub\Client\User;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
	
	public function testFindKeyByTypeWhenFoundThenReturnKey() {
		$key = StandardUserKeyFactory::email('example@example.com');
		$user = new User([$key]);
		$this->assertSame($key, $user->findKeyByType(StandardUserKeyFactory::EMAIL_KEY_TYPE));
	}
	
	public function testFindKeyByTypeWhenNotFoundThenReturnNull() {
		$key = StandardUserKeyFactory::email('example@example.com');
		$user = new User([$key]);
		$this->assertNull($user->findKeyByType(StandardUserKeyFactory::COOKIE_KEY_TYPE));
	}
}
