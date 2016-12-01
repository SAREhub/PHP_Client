<?php

namespace SAREhub\Client\Message;

/**
 * Basic implementation of Message interface
 */
class BasicMessage implements Message {
	
	protected $headers = [];
	protected $body = null;
	
	/**
	 * @param mixed $body
	 * @return $this
	 */
	public static function withBody($body) {
		return (new self())->setBody($body);
	}
	
	public function getHeader($name, $defaultValue = null) {
		return $this->hasHeader($name) ? $this->headers[$name] : $defaultValue;
	}
	
	public function hasHeader($name) {
		return $this->hasAnyHeader() && isset($this->headers[$name]);
	}
	
	public function getHeaders() {
		return $this->hasAnyHeader() ? $this->headers : [];
	}
	
	public function hasAnyHeader() {
		return !empty($this->headers);
	}
	
	public function setHeaders(array $headers) {
		$this->headers = $headers;
		return $this;
	}
	
	public function setHeader($name, $value) {
		$this->headers[$name] = $value;
		return $this;
	}
	
	public function removeHeader($name) {
		unset($this->headers[$name]);
		return $this;
	}
	
	public function getBody() {
		return $this->body;
	}
	
	public function hasBody() {
		return $this->body !== null;
	}
	
	public function setBody($body) {
		$this->body = $body;
		return $this;
	}
}
