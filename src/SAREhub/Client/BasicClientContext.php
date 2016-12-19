<?php

namespace SAREhub\Client;

use SAREhub\Client\Util\LoggerFactory;
use SAREhub\Commons\Misc\TimeProvider;
use SAREhub\Component\Worker\Service\Service;
use SAREhub\Component\Worker\Service\ServiceSupport;

class BasicClientContext extends ServiceSupport implements ClientContext {
	
	/**
	 * @var array
	 */
	private $properties = [];
	
	/**
	 * @var Service[]
	 */
	private $services = [];
	
	/**
	 * @var TimeProvider
	 */
	private $timeProvider;
	
	/**
	 * @var LoggerFactory
	 */
	private $loggerFactory;
	
	public function __construct() {
		$this->timeProvider = TimeProvider::get();
	}
	
	/**
	 * @return BasicClientContext
	 */
	public static function newInstance() {
		return new self();
	}
	
	/**
	 * @param TimeProvider $provider
	 * @return $this
	 */
	public function withTimeProvider(TimeProvider $provider) {
		$this->timeProvider = $provider;
		return $this;
	}
	
	/**
	 * @param LoggerFactory $factory
	 * @return $this
	 */
	public function withLoggerFactory(LoggerFactory $factory) {
		$this->loggerFactory = $factory;
		return $this;
	}
	
	public function getProperty($name) {
		if ($this->hasProperty($name)) {
			return $this->properties[$name];
		}
		
		throw new \OutOfBoundsException("Property [$name] isn't exists in context");
	}
	
	public function hasProperty($name) {
		return isset($this->properties[$name]);
	}
	
	public function setProperty($name, $value) {
		$this->properties[$name] = $value;
	}
	
	public function registerService($name, Service $service) {
		$this->services[$name] = $service;
	}
	
	public function getService($name) {
		if ($this->hasService($name)) {
			return $this->services[$name];
		}
		
		throw new \OutOfBoundsException("Service [$name] isn't registered");
	}
	
	public function hasService($name) {
		return isset($this->services[$name]);
	}
	
	public function getServices() {
		return $this->services;
	}
	
	public function getTimeProvider() {
		return $this->timeProvider;
	}
	
	public function createLogger($name) {
		if ($this->loggerFactory === null) {
			throw new \LogicException("Logger factory isn't sets");
		}
		
		return $this->loggerFactory->create($name);
	}
	
	protected function doStart() {
		foreach ($this->getServices() as $service) {
			$service->start();
		}
	}
	
	protected function doTick() {
		foreach ($this->getServices() as $service) {
			$service->tick();
		}
	}
	
	protected function doStop() {
		foreach ($this->getServices() as $service) {
			$service->stop();
		}
	}
}