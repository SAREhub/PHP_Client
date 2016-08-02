<?php


namespace SAREhub\Client\User;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
	
	public function testFindKeyByType() {
		$userKey = new EmailUserKey('example@example.com');
		$user = new User([
		  $userKey
		]);
		
		$this->assertSame($userKey, $user->findKeyByClass(EmailUserKey::class));
	}
}
