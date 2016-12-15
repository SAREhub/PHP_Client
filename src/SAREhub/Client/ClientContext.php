<?php

namespace SAREhub\Client;


use Psr\Log\LoggerInterface;
use SAREhub\Client\Util\LoggerFactory;
use SAREhub\Commons\Misc\TimeProvider;
use SAREhub\Component\Worker\Service\Service;

interface ClientContext extends Service {
	
	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getProperty($name);
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasProperty($name);
	
	/**
	 * @param $name
	 * @param $value
	 * @return mixed
	 */
	public function setProperty($name, $value);
	
	
	/**
	 * @param string $name
	 * @param Service $service
	 * @return $this
	 */
	public function registerService($name, Service $service);
	
	
	/**
	 * @param string $name
	 * @return Service
	 */
	public function getService($name);
	
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasService($name);
	
	/**
	 * @return TimeProvider
	 */
	public function getTimeProvider();
	
	
	/**
	 * @param TimeProvider $provider
	 */
	public function setTimeProvider(TimeProvider $provider);
	
	/**
	 * @param string $name
	 * @return LoggerInterface
	 */
	public function createLogger($name);
	
	/**
	 * @return LoggerFactory
	 */
	public function getLoggerFactory();
}