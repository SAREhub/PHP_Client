<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;

interface Processor {
	
	public function process(Exchange $exchange);
}