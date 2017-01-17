<?php

namespace SAREhub\Client\DataFormat;

use SAREhub\Client\Message\Exchange;

class JsonDataFormat implements DataFormat {
	
	/**
	 * @param Exchange $exchange
	 * @return string
	 */
	public function marshal(Exchange $exchange) {
		return json_encode($exchange->getIn()->getBody());
	}
	
	/**
	 * @param Exchange $exchange
	 * @return array
	 */
	public function unmarshal(Exchange $exchange) {
		return json_decode($exchange->getIn()->getBody(), true);
	}
	
	public function __toString() {
		return 'DataFormat[JSON]';
	}
}