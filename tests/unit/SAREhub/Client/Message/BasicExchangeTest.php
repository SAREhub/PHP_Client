<?php

namespace unit\SAREhub\Client\Message;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class BasicExchangeTest extends TestCase
{

    /**
     * @var BasicExchange
     */
    private $exchange;

    protected function setUp()
    {
        $this->exchange = new BasicExchange();
    }

    public function testWithInThenGetIn()
    {
        $message = new BasicMessage();
        $this->assertSame($message, BasicExchange::withIn($message)->getIn());
    }

    public function testIsFailedWhenNoExceptionSetsThenReturnFalse()
    {
        $this->assertFalse($this->exchange->isFailed());
    }

    public function testIsFailedWhenExceptionSetsThenReturnTrue()
    {
        $this->exchange->setException(new \Exception());
        $this->assertTrue($this->exchange->isFailed());
    }

    public function testJsonSerializeWhenEmptyOut()
    {
        $expected = [
            "in" => null,
            "out" => null,
            "exception" => null
        ];

        $this->assertEquals($expected, $this->exchange->jsonSerialize());
    }
}
