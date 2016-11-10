<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;

/**
 * That processor execute transform function on exchange.
 */
class TransformProcessor implements Processor {
	
	private $tranformer;
	
	public function __construct(callable $tranformer) {
		$this->tranformer = $tranformer;
	}
	
	/**
	 * @param callable $tranformer
	 * @return TransformProcessor
	 */
	public static function transform(callable $tranformer) {
		return new self($tranformer);
	}
	
	public function process(Exchange $exchange) {
		$c = $this->tranformer;
		$c($exchange);
	}
}