<?php

namespace SAREhub\Client;

use Psr\Log\LoggerAwareInterface;
use SAREhub\Commons\Logger\LoggerFactory;
use SAREhub\Commons\Misc\TimeProvider;
use SAREhub\Commons\Service\Service;

interface ClientContext extends Service
{

    /**
     * Sets property in context
     * @param string $name
     * @return mixed
     */
    public function getProperty($name);

    /**
     * Checks context has property
     * @param string $name
     * @return bool
     */
    public function hasProperty($name);

    /**
     * Sets propertty yin context
     * Value can be callable(first argument will be $this context),
     * it will be lazy evaluate in first use.
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setProperty($name, $value);

    /**
     * Adds factory to context as property any call to that
     * @param string $name
     * @param callable $factory
     * @return $this
     */
    public function addFactory($name, callable $factory);

    /**
     * Adds service to context for lifecycle manage(service will be started if not started yet).
     * Sets service as context property.
     * Sets context(when implements ClientContextAware) and logger(creates based on name) of service.
     *
     * @param string $name
     * @param Service $service
     * @return $this
     */
    public function addService($name, Service $service);

    /**
     * @param ContextProvider $provider
     * @return $this
     */
    public function registerContextProvider(ContextProvider $provider);

    /**
     * Returns current timestamp
     * @return int
     */
    public function now();

    /**
     * @return TimeProvider
     */
    public function getTimeProvider();

    /**
     * @param TimeProvider $provider
     * @return $this
     */
    public function setTimeProvider(TimeProvider $provider);

    /**
     * @param string $name
     * @param LoggerAwareInterface $aware
     */
    public function injectLogger($name, LoggerAwareInterface $aware);

    /**
     * @return LoggerFactory
     */
    public function getLoggerFactory();

    /**
     * @param LoggerFactory $factory
     * @return $this
     */
    public function setLoggerFactory(LoggerFactory $factory);

}