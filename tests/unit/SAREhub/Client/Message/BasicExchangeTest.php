<?php

namespace unit\SAREhub\Client\Message;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class BasicExchangeTest extends TestCase {
	
	public function testWithInThenGetIn() {
		$message = new BasicMessage();
		$this->assertSame($message, BasicExchange::withIn($message)->getIn());
	}
}
