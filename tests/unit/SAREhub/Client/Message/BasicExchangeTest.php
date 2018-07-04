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

    public function testGetInWhenNotSetsThenNewInstanceCreated()
    {
        $this->assertInstanceOf(BasicMessage::class, BasicExchange::newInstance()->getIn());
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
        $data = $this->exchange->jsonSerialize();
        $this->assertInstanceOf(BasicMessage::class, $data["in"], "data.in");
        $this->assertNull($data["out"]);
        $this->assertNull($data["exception"]);
    }


    public function testCopyThenIsNotSameInstance()
    {
        $original = BasicExchange::withIn(BasicMessage::withBody("test"));
        $copy = $original->copy();
        $this->assertNotSame($original, $copy);
    }

    public function testCopyThenInCopied()
    {
        $original = BasicExchange::withIn(BasicMessage::withBody("test"));
        $copy = $original->copy();
        $this->assertNotSame($original->getIn(), $copy->getIn());
        $this->assertEquals($original->getIn(), $copy->getIn());
    }

    public function testCopyWhenHasOutThenOutCopied()
    {
        $original = BasicExchange::withIn(BasicMessage::withBody("in_body"));
        $original->getOut()->setBody("out_body");
        $copy = $original->copy();
        $this->assertNotSame($original->getOut(), $copy->getOut());
        $this->assertEquals($original->getOut(), $copy->getOut());
    }

    public function testCopyWhenHasNotOutThenCopiedIsWithoutOut()
    {
        $original = BasicExchange::withIn(BasicMessage::withBody("in_body"));
        $copy = $original->copy();
        $this->assertFalse($copy->hasOut());
    }

    public function testCopyWhenHasException()
    {
        $original = BasicExchange::withIn(BasicMessage::withBody("in_body"));
        $exception = new \Exception("test");
        $original->setException($exception);
        $copy = $original->copy();
        $this->assertSame($exception, $copy->getException());
    }
}
