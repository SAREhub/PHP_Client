<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\DataFormat\DataFormat;
use SAREhub\Client\Message\Exchange;

/**
 * Marshaling exchange input message body with selected DataFormat
 */
class MarshalProcessor implements Processor {
	
	/**
	 * @var DataFormat
	 */
	protected $dataFormat;
	
	public function __construct(DataFormat $dataFormat) {
		$this->dataFormat = $dataFormat;
	}
	
	/**
	 * @param DataFormat $dataFormat
	 * @return MarshalProcessor
	 */
	public static function withDataFormat(DataFormat $dataFormat) {
		return new self($dataFormat);
	}
	
	public function process(Exchange $exchange) {
		$this->getDataFormat()->marshal($exchange);
	}
	
	/**
	 * @return DataFormat
	 */
	public function getDataFormat() {
		return $this->dataFormat;
	}
	
	public function __toString() {
		return 'Marshal['.$this->getDataFormat().']';
	}
}