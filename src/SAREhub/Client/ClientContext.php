<?php

namespace SAREhub\Client;

use Psr\Log\LoggerInterface;
use SAREhub\Commons\Misc\TimeProvider;
use SAREhub\Component\Worker\Service\Service;

interface ClientContext extends Service {
	
	/**
	 * @param string $name
	 * @return mixed
	 * @throws \OutOfBoundsException
	 */
	public function getProperty($name);
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasProperty($name);
	
	/**
	 * @param string $name
	 * @param mixed $value
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
	 * @throws \OutOfBoundsException
	 */
	public function getService($name);
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasService($name);
	
	/**
	 * @return Service[]
	 */
	public function getServices();
	
	/**
	 * @return TimeProvider
	 */
	public function getTimeProvider();
	
	/**
	 * @param string $name
	 * @return LoggerInterface
	 * @throws \Exception
	 */
	public function createLogger($name);
}