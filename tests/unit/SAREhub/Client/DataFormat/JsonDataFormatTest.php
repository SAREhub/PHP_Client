<?php

namespace SAREhub\Client\DataFormat;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class JsonDataFormatTest extends TestCase {
	
	/**
	 * @var DataFormat
	 */
	private $dataFormat;
	
	protected function setUp() {
		$this->dataFormat = new JsonDataFormat();
	}
	
	public function testMarshalThenExchangeOutBody() {
		$data = ['param1' => 1, 'param2' => 2];
		$exchange = BasicExchange::withIn(BasicMessage::withBody($data));
		$this->dataFormat->marshal($exchange);
		$this->assertEquals(json_encode($data), $exchange->getOut()->getBody());
	}
	
	public function testUnmarshalThenExchangeOutBody() {
		$data = ['param1' => 1, 'param2' => 2];
		$exchange = BasicExchange::withIn(BasicMessage::withBody(json_encode($data)));
		$this->dataFormat->unmarshal($exchange);
		$this->assertEquals($data, $exchange->getOut()->getBody());
	}
}
