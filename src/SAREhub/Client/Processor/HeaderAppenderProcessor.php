<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;

/**
 * Appends defined headers to input message.
 */
class HeaderAppenderProcessor implements Processor {
	
	private $headers = [];
	
	public static function newInstance() {
		return new self();
	}
	
	/**
	 * @param array $headers
	 * @return $this
	 */
	public function withHeaders(array $headers) {
		$this->headers = $headers;
		return $this;
	}
	
	public function process(Exchange $exchange) {
		$in = $exchange->getIn();
		foreach ($this->getHeaders() as $header => $value) {
			$in->setHeader($header, $value);
		}
	}
	
	/**
	 * @return array
	 */
	public function getHeaders() {
		return $this->headers;
	}
	
	public function __toString() {
		return 'HeaderAppender['.http_build_query($this->getHeaders(), '', ', ').']';
	}
}