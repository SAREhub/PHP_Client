<?php

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Message;

class BasicMessageTest extends TestCase {
	
	/**
	 * @var Message
	 */
	private $message;
	
	public function setUp() {
		$this->message = new BasicMessage();
	}
	
	public function testSetHeaderThenReturnThis() {
		$this->assertSame($this->message, $this->message->setHeader('test', 1));
	}
	
	public function testSetHeaderThenHeaderSets() {
		$this->message->setHeader('test', 1);
		$this->assertSame(1, $this->message->getHeader('test'));
	}
	
	public function testGetHeadersWhenDontHaveAny() {
		$this->assertEquals([], $this->message->getHeaders());
	}
	
	public function testSetBodyThenReturnThis() {
		$this->assertSame($this->message, $this->message->setBody("body"));
	}
	
	public function testSetBodyThenBodySets() {
		$this->message->setBody("body");
		$this->assertEquals("body", $this->message->getBody());
	}
	
	public function testSetBodyThenHasBodyReturnTrue() {
		$this->message->setBody("body");
		$this->assertTrue($this->message->hasBody());
	}
	
	public function testHasBodyWhenNotSetsThenReturnFalse() {
		$this->assertFalse($this->message->hasBody());
	}
	
	public function testRemoveHeaderThenReturnThis() {
		$this->message->setHeader('test1', 1);
		$this->assertSame($this->message, $this->message->removeHeader('test1'));
	}
	
	public function testRemoveHeaderThenHeaderRemoved() {
		$this->message->removeHeader('test1', 1);
		$this->assertFalse($this->message->hasHeader('test1'));
	}
}
