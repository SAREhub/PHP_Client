<?php

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Processor\Processor;
use SAREhub\Client\Processor\Router;

class RouterTest extends TestCase {
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $routingFunctionMock;
	
	/**
	 * @var Router
	 */
	private $router;
	
	private $routingKey = 'key';
	
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $routeMock;
	
	
	public function setUp() {
		$this->routingFunctionMock = $this->getMockBuilder(stdClass::class)->setMethods(['__invoke'])->getMock();
		$this->router = Router::withRoutingFunction($this->routingFunctionMock);
		$this->routeMock = $this->createMock(Processor::class);
		$this->router->addRoute($this->routingKey, $this->routeMock);
	}
	
	public function testAddRouteThenHasRoute() {
		$this->assertTrue($this->router->hasRoute($this->routingKey));
	}
	
	public function testAddRouteThenReturnThis() {
		$this->assertSame($this->routeMock, $this->router->getRoute($this->routingKey));
	}
	
	public function testRemoveRouteThenHasRouteReturnFalse() {
		$route = $this->createMock(Processor::class);
		$routingKey = 'key';
		$this->router->addRoute($routingKey, $route);
		$this->router->removeRoute($routingKey);
		$this->assertFalse($this->router->hasRoute($routingKey));
		$this->assertNull($this->router->getRoute($routingKey));
	}
	
	public function testRemoveRouteThenGetRouteReturnNull() {
		$route = $this->createMock(Processor::class);
		$routingKey = 'key';
		$this->router->addRoute($routingKey, $route);
		$this->router->removeRoute($routingKey);
		$this->assertNull($this->router->getRoute($routingKey));
	}
	
	public function testRemoveRouteWhenNotExists() {
		$routingKey = 'key';
		$this->router->removeRoute($routingKey);
		$this->assertFalse($this->router->hasRoute($routingKey));
	}
	
	public function testProcess() {
		$exchange = new BasicExchange();
		$this->routingFunctionMock->expects($this->once())
		  ->method('__invoke')
		  ->with($exchange)
		  ->willReturn($this->routingKey);
		
		$this->routeMock->expects($this->once())
		  ->method('process')
		  ->with($exchange);
		
		$this->router->process($exchange);
	}
}
