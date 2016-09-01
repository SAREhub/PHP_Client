<?php

namespace SAREhub\Client\Amqp;

use PHPUnit\Framework\TestCase;

class RoutingKeyTest extends TestCase {
	
	public function testCreateEmpty() {
		$routingKey = new RoutingKey();
		$this->assertEquals([], $routingKey->getParts());
	}
	
	public function testCreateFromString() {
		$routingKey = new RoutingKey('p1.p2');
		$this->assertEquals(['p1', 'p2'], $routingKey->getParts());
	}
	
	public function testCreateFromArray() {
		$routingKey = new RoutingKey(['p1', 'p2']);
		$this->assertEquals(['p1', 'p2'], $routingKey->getParts());
	}
	
	public function testAddPart() {
		$routingKey = new RoutingKey(['p1']);
		$routingKey->addPart('p2');
		$this->assertEquals(['p1', 'p2'], $routingKey->getParts());
	}
	
	public function testGetPart() {
		$routingKey = new RoutingKey(['p1', 'p2']);
		$this->assertEquals('p2', $routingKey->getPart(1));
	}
	
	public function testGetPartNotExists() {
		$routingKey = new RoutingKey(['p1', 'p2']);
		$this->assertEmpty($routingKey->getPart(2));
	}
	
	public function testIsEmpty() {
		$routingKey = new RoutingKey();
		$this->assertTrue($routingKey->isEmpty());
	}
	
	public function testGetIterator() {
		$routingKey = new RoutingKey(['p1', 'p2']);
		$this->assertEquals(['p1', 'p2'], $routingKey->getIterator());
	}
	
	public function testToString() {
		$routingKey = new RoutingKey(['p1', 'p2', 'p3']);
		$this->assertEquals('p1.p2.p3', (string)$routingKey);
	}
}
