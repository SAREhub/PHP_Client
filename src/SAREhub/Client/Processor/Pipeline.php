<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;

class Pipeline implements Processor {
	
	/**
	 * @var Processor[]
	 */
	protected $processors = [];
	
	/**
	 * @return Pipeline
	 */
	public static function start() {
		return new self();
	}
	
	public function process(Exchange $exchange) {
		$currentExchange = $exchange;
		$orginalMessage = $currentExchange->getIn();
		
		$isFirstTime = true;
		foreach ($this->getProcessors() as $processor) {
			if ($isFirstTime) {
				$isFirstTime = false;
			} else {
				$currentExchange = $this->createNextExchange($currentExchange);
			}
			
			$processor->process($currentExchange);
		}
		
		$currentExchange->setIn($orginalMessage);
	}
	
	protected function createNextExchange(Exchange $previousExchange) {
		$out = $previousExchange->getOut();
		$previousExchange->clearOut();
		return $previousExchange->setIn($out);
	}
	
	/**
	 * @param Processor $processor
	 * @return $this
	 */
	public function add(Processor $processor) {
		$this->processors[] = $processor;
		return $this;
	}
	
	/**
	 * @param array $processors
	 * @return $this
	 */
	public function addAll(array $processors) {
		foreach ($processors as $processor) {
			$this->add($processor);
		}
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function clear() {
		$this->processors = [];
		return $this;
	}
	
	/**
	 * @return Processor[]
	 */
	public function getProcessors() {
		return $this->processors;
	}
}