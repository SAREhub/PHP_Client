<?php

namespace SAREhub\Client\Util;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

class BasicLoggerFactory implements LoggerFactory {
	
	private $handlers = [];
	private $processors = [];
	
	/**
	 * @return BasicLoggerFactory
	 */
	public static function newInstance() {
		return new self();
	}
	
	/**
	 * @param HandlerInterface $handler
	 * @return $this
	 */
	public function addHandler(HandlerInterface $handler) {
		$this->handlers[] = $handler;
		return $this;
	}
	
	/**
	 * @param callable $processor
	 * @return $this
	 */
	public function addProcessor(callable $processor) {
		$this->processors[] = $processor;
		return $this;
	}
	
	public function create($name) {
		return new Logger($name, $this->getHandlers(), $this->getProcessors());
	}
	
	public function getHandlers() {
		return $this->handlers;
	}
	
	public function getProcessors() {
		return $this->processors;
	}
}