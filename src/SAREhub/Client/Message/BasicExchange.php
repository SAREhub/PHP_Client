<?php

namespace SAREhub\Client\Message;

use Prophecy\Exception\Exception;

/**
 * Basic implementation of Exchange interface
 */
class BasicExchange implements Exchange, \JsonSerializable {
	
	/**
	 * @var Message|null
	 */
	private $in = null;
	
	/**
	 * @var Message|null
	 */
	private $out = null;
	
	/**
	 * @var Exception|null
	 */
	private $exception = null;
	
	/**
	 * @return BasicExchange
	 */
	public static function newInstance() {
		return new self();
	}
	
	public static function withIn(Message $message) {
		$exchange = new self();
		$exchange->setIn($message);
		return $exchange;
	}
	
	public function getIn() {
		return $this->in;
	}
	
	public function setIn(Message $message) {
		$this->in = $message;
		return $this;
	}
	
	public function getOut() {
		if (!$this->hasOut()) {
			$this->setOut(new BasicMessage());
		}
		return $this->out;
	}
	
	public function setOut(Message $message) {
		$this->out = $message;
		return $this;
	}
	
	public function hasOut() {
		return $this->out !== null;
	}
	
	public function clearOut() {
		$this->out = null;
		return $this;
	}
	
	public function isFailed() {
		return $this->getException() !== null;
	}
	
	public function getException() {
		return $this->exception;
	}
	
	public function setException(\Exception $exception) {
		$this->exception = $exception;
	}
	
	public function jsonSerialize() {
		return [
		  "in" => $this->getIn(),
		  "out" => $this->getOut(),
		  "exception" => $this->getException()
		];
	}
}

