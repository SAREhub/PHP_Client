<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\DataFormat\DataFormat;
use SAREhub\Client\Message\Exchange;

class UnmarshalProcessor implements Processor {
	
	protected $dataFormat;
	
	public function __construct(DataFormat $dataFormat) {
		$this->dataFormat = $dataFormat;
	}
	
	/**
	 * @param DataFormat $dataFormat
	 * @return UnmarshalProcessor
	 */
	public static function withDataFormat(DataFormat $dataFormat) {
		return new self($dataFormat);
	}
	
	public function process(Exchange $exchange) {
		$exchange->setOut($exchange->getIn()->copy());
		$unmarshaled = $this->dataFormat->unmarshal($exchange);
		$exchange->getOut()->setBody($unmarshaled);
	}
	
	/**
	 * @return DataFormat
	 */
	public function getDataFormat() {
		return $this->dataFormat;
	}
	
	public function __toString() {
		return 'Unmarshal['.$this->getDataFormat().']';
	}
}