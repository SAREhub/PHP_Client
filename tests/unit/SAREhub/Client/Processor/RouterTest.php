<?php

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Processor\Processor;
use SAREhub\Client\Processor\Router;

class RouterTest extends TestCase
{

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


    public function setUp()
    {
        $this->routingFunctionMock = $this->getMockBuilder(stdClass::class)->setMethods(['__invoke'])->getMock();
        $this->router = Router::newInstance()->withRoutingFunction($this->routingFunctionMock);
        $this->routeMock = $this->createMock(Processor::class);
    }

    public function testAddRouteThenHasRoute()
    {
        $this->router->addRoute($this->routingKey, $this->routeMock);
        $this->assertTrue($this->router->hasRoute($this->routingKey));
    }

    public function testAddRouteThenReturnThis()
    {
        $this->router->addRoute($this->routingKey, $this->routeMock);
        $this->assertSame($this->routeMock, $this->router->getRoute($this->routingKey));
    }

    public function testRemoveRouteThenHasRouteReturnFalse()
    {
        $this->router->addRoute($this->routingKey, $this->routeMock);
        $route = $this->createMock(Processor::class);
        $routingKey = 'key';
        $this->router->addRoute($routingKey, $route);
        $this->router->removeRoute($routingKey);
        $this->assertFalse($this->router->hasRoute($routingKey));
        $this->assertNull($this->router->getRoute($routingKey));
    }

    public function testRemoveRouteThenGetRouteReturnNull()
    {
        $this->router->addRoute($this->routingKey, $this->routeMock);
        $route = $this->createMock(Processor::class);
        $routingKey = 'key';
        $this->router->addRoute($routingKey, $route);
        $this->router->removeRoute($routingKey);
        $this->assertNull($this->router->getRoute($routingKey));
    }

    public function testRemoveRouteWhenNotExists()
    {
        $this->router->addRoute($this->routingKey, $this->routeMock);
        $routingKey = 'key';
        $this->router->removeRoute($routingKey);
        $this->assertFalse($this->router->hasRoute($routingKey));
    }

    public function testProcess()
    {
        $this->router->addRoute($this->routingKey, $this->routeMock);
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

    public function testToString()
    {
        $this->router
            ->addRoute('1', $this->createRoute('r1'))
            ->addRoute('2', $this->createRoute('r2'));
        $this->assertEquals("Router[ {1 => r1}, {2 => r2}]", (string)$this->router);

    }

    private function createRoute($name)
    {
        $route = $this->createPartialMock(Processor::class, ['process', '__toString']);
        $route->method('__toString')->willReturn($name);
        return $route;
    }
}
