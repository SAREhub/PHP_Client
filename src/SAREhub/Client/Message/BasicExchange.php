<?php

namespace SAREhub\Client\Message;

/**
 * Basic implementation of Exchange interface
 */
class BasicExchange implements Exchange {
	
	protected $in = null;
	protected $out = null;
	
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
	
	public function hasOut() {
		return $this->out !== null;
	}
	
	public function setOut(Message $message) {
		$this->out = $message;
		return $this;
	}
	
	public function clearOut() {
		$this->out = null;
		return $this;
	}
}
