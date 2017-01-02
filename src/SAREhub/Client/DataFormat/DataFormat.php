<?php

namespace SAREhub\Client\DataFormat;

use SAREhub\Client\Message\Exchange;

/**
 * Represents data format who can be marshaled and unmarshaled.
 */
interface DataFormat {
	
	/**
	 * @param Exchange $exchange
	 */
	public function marshal(Exchange $exchange);
	
	/**
	 * @param Exchange $exchange
	 */
	public function unmarshal(Exchange $exchange);
	
	public function __toString();
}