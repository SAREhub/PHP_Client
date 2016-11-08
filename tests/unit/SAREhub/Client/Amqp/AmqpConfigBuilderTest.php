<?php


namespace SAREhub\Client\Amqp;

use PHPUnit\Framework\TestCase;

class AmqpConfigBuilderTest extends TestCase {
	
	
	public function testBuild() {
		$config = [
		  'host' => 'host123',
		  'port' => 10000,
		  'username' => 'user',
		  'password' => 'pass',
		  'vhost' => 'vhost123',
		  'heartbeat' => 30,
		  'keepalive' => true
		];
		
		$this->assertEquals($config, (new AmqpConfigBuilder())
		  ->host($config['host'])
		  ->port($config['port'])
		  ->username($config['username'])
		  ->password($config['password'])
		  ->vhost($config['vhost'])
		  ->build()->getAll()
		);
	}
}
