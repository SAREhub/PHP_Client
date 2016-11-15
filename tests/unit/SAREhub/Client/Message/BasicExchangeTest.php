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
	
	public function testIsFailedWhenNoExceptionSetsThenReturnFalse() {
		$exchange = new BasicExchange();
		$this->assertFalse($exchange->isFailed());
	}
	
	public function testIsFailedWhenExceptionSetsThenReturnTrue() {
		$exchange = new BasicExchange();
		$exchange->setException(new \Exception());
		$this->assertTrue($exchange->isFailed());
	}
}
