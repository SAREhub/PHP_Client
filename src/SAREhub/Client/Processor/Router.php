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
class Router implements Processor, LoggerAwareInterface {
	
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
	
	public function __construct() {
		$this->logger = new NullLogger();
	}
	
	/**
	 * @return Router
	 */
	public static function newInstance() {
		return new self();
	}
	
	/**
	 * @param callable $f
	 * @return $this
	 */
	public function withRoutingFunction(callable $f) {
		$this->routingFunction = $f;
		return $this;
	}
	
	public function process(Exchange $exchange) {
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
	
	/**
	 * @param Exchange $exchange
	 * @return string
	 */
	public function getRoutingKeyForExchange(Exchange $exchange) {
		$routingFunction = $this->routingFunction;
		return $routingFunction($exchange);
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
	
	public function __toString() {
		return 'Router['.implode(',', $this->getRoutes()).']';
	}
	
	public function getLogger() {
		return $this->logger;
	}
	
	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}
}