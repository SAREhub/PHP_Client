<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PHPUnit\Framework\TestCase;

class AmqpEventStreamSinkTest extends TestCase {
	
	public function testWrite() {
		$channelMock = $this->getMockBuilder(AMQPChannel::class)->disableOriginalConstructor()->setMethods(['basic_'])->getMock();
	}
}
