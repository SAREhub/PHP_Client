<?php

namespace SAREhub\Client\DataFormat;

use SAREhub\Client\Message\Exchange;

class JsonDataFormat implements DataFormat {
	
	/**
	 * @param Exchange $exchange
	 */
	public function marshal(Exchange $exchange) {
		$marshaled = json_encode($exchange->getIn()->getBody());
		$exchange->getOut()->setBody($marshaled);
	}
	
	/**
	 * @param Exchange $exchange
	 */
	public function unmarshal(Exchange $exchange) {
		$unmarshaled = json_decode($exchange->getIn()->getBody(), true);
		$exchange->getOut()->setBody($unmarshaled);
	}
}