<?php

namespace SAREhub\Client\Processor;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SAREhub\Client\Message\Exchange;

/**
 * Basic router processor.
 * Routing exchanges to processor defined by routing key value returns by routingFunction.
 */
class Router implements Processor, LoggerAwareInterface
{

    /**
     * @var Processor[]
     */
    private $routes = [];

    /**
     * @var callable
     */
    private $routingFunction;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(callable $routingFunction)
    {
        $this->routingFunction = $routingFunction;
        $this->logger = new NullLogger();
    }

    public static function withRoutingFunction(callable $routingFunction): Router
    {
        return new self($routingFunction);
    }

    public function process(Exchange $exchange)
    {
        $routingKey = $this->getRoutingKeyForExchange($exchange);

        if ($route = $this->getRoute($routingKey)) {
            $this->getLogger()->debug('exchange has route', [
                'routingKey' => $routingKey,
                'exchange' => $exchange,
                'route' => $route
            ]);
            $route->process($exchange);
        } else {
            $this->getLogger()->debug('route for exchange not found', [
                'routingKey' => $routingKey,
                'exchange' => $exchange
            ]);
        }
    }

    public function getRoutingKeyForExchange(Exchange $exchange): string
    {
        $routingFunction = $this->routingFunction;
        return $routingFunction($exchange);
    }

    public function addRoute($routingKey, Processor $route): Router
    {
        $this->routes[$routingKey] = $route;
        return $this;
    }

    public function removeRoute($routingKey)
    {
        unset($this->routes[$routingKey]);
    }

    public function getRoute($routingKey): ?Processor
    {
        return $this->hasRoute($routingKey) ? $this->routes[$routingKey] : null;
    }

    public function hasRoute($routingKey): bool
    {
        return isset($this->routes[$routingKey]);
    }

    /**
     * @return Processor[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getRoutingFunction(): callable
    {
        return $this->routingFunction;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __toString()
    {
        $routes = [];
        foreach ($this->getRoutes() as $key => $route) {
            $routes[] = $key . ' => ' . $route;
        }

        return 'Router[ {' . implode('}, {', $routes) . '}]';
    }
}