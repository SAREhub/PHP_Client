<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;

/**
 * Basic router processor.
 * Routing exchanges to processor defined by routing key value returns by routingFunction.
 */
class Router implements Processor {
	
	/** @var Processor[] */
	protected $routes = [];
	
	protected $routingFunction;
	
	/**
	 * @param callable $routingFunction
	 */
	public function __construct(callable $routingFunction) {
		$this->routingFunction = $routingFunction;
	}
	
	/**
	 * @param callable $routingFunction
	 * @return Router
	 */
	public static function withRoutingFunction(callable $routingFunction) {
		return new self($routingFunction);
	}
	
	public function process(Exchange $exchange) {
		if ($route = $this->getRoute($this->getRoutingKeyForExchange($exchange))) {
			$route->process($exchange);
		}
	}
	
	/**
	 * @param Exchange $exchange
	 * @return string
	 */
	public function getRoutingKeyForExchange(Exchange $exchange) {
		return ($this->routingFunction)($exchange);
	}
	
	/**
	 * @param string $routingKey
	 * @param Processor $route
	 * @return $this
	 */
	public function addRoute($routingKey, Processor $route) {
		$this->routes[$routingKey] = $route;
		return $this;
	}
	
	/**
	 * @param string $routingKey
	 */
	public function removeRoute($routingKey) {
		unset($this->routes[$routingKey]);
	}
	
	/**
	 * @param string $routingKey
	 * @return null|Processor
	 */
	public function getRoute($routingKey) {
		return $this->hasRoute($routingKey) ? $this->routes[$routingKey] : null;
	}
	
	/**
	 * @param string $routingKey
	 * @return bool
	 */
	public function hasRoute($routingKey) {
		return isset($this->routes[$routingKey]);
	}
	
	/**
	 * @return Processor[]
	 */
	public function getRoutes() {
		return $this->routes;
	}
	
	/**
	 * @return callable
	 */
	public function getRoutingFunction() {
		return $this->routingFunction;
	}
}