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
	private $dataFormat;
	
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
		$exchange->setOut($exchange->getIn()->copy());
		$marshaled = $this->getDataFormat()->marshal($exchange);
		$exchange->getOut()->setBody($marshaled);
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