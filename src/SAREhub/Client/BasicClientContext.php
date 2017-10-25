<?php

namespace SAREhub\Client;

use Pimple\Container;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SAREhub\Client\Util\LoggerFactory;
use SAREhub\Client\Util\NullLoggerFactory;
use SAREhub\Commons\Misc\TimeProvider;
use SAREhub\Commons\Service\Service;

class BasicClientContext extends Container implements ClientContext
{

    /**
     * @var Service[]
     */
    private $services = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TimeProvider
     */
    private $timeProvider;

    /**
     * @var LoggerFactory
     */
    private $loggerFactory;

    private $started = false;

    public function __construct()
    {
        parent::__construct();

        $this->timeProvider = new TimeProvider();
        $this->loggerFactory = new NullLoggerFactory();
        $this->logger = $this->loggerFactory->create('ClientContext');
    }

    /**
     * @return BasicClientContext
     */
    public static function newInstance()
    {
        return new self();
    }

    public function getProperty($name)
    {
        return $this[$name];
    }

    public function hasProperty($name)
    {
        return $this->offsetExists($name);
    }

    public function setProperty($name, $value)
    {
        $this[$name] = $value;
        return $this;
    }

    public function addFactory($name, callable $factory)
    {
        $this->setProperty($name, $this->factory($factory));
        return $this;
    }

    public function addService($name, Service $service)
    {
        $this->setProperty($name, $service);
        $this->services[$name] = $service;
        if ($service instanceof ClientContextAware) {
            $service->setClientContext($this);
        }

        $this->injectLogger($name, $service);
        $service->start();

        return $this;
    }

    public function now()
    {
        return $this->getTimeProvider()->now();
    }

    public function start()
    {
        $this->started = true;
    }

    public function tick()
    {
        foreach ($this->services as $service) {
            $service->tick();
        }
    }

    public function stop()
    {
        foreach ($this->services as $service) {
            $service->stop();
        }
    }

    public function isStarted()
    {
        return $this->started;
    }

    public function isStopped()
    {
        return !$this->isStarted();
    }

    public function isRunning()
    {
        return $this->isStarted();
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLoggerFactory()
    {
        return $this->loggerFactory;
    }

    public function setLoggerFactory(LoggerFactory $factory)
    {
        $this->loggerFactory = $factory;
        return $this;
    }

    public function getTimeProvider()
    {
        return $this->timeProvider;
    }

    public function setTimeProvider(TimeProvider $provider)
    {
        $this->timeProvider = $provider;
        return $this;
    }

    public function injectLogger($name, LoggerAwareInterface $aware)
    {
        $logger = $this->getLoggerFactory()->create($name);
        $aware->setLogger($logger);
    }

    /**
     * @param ContextProvider $provider
     * @return $this
     */
    public function registerContextProvider(ContextProvider $provider)
    {
        $provider->register($this);
        return $this;
    }
}