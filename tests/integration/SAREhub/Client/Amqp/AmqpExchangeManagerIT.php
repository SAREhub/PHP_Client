<?php

namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PHPUnit\Framework\TestCase;

class AmqpExchangeManagerIT extends TestCase
{
    /**
     * @var AMQPChannel
     */
    private $channel;

    private $exchangeName = "AmqpExchangeManagerIT";

    /**
     * @var AmqpExchangeManager
     */
    private $exchangeManager;

    protected function setUp()
    {
        $connection = AmqpTestHelper::createConnection();
        $this->channel = $connection->channel();

        $this->exchangeManager = new AmqpExchangeManager($this->channel);
        $this->channel->exchange_delete($this->exchangeName);
    }

    public function testCreateWhenNotExistsThenCreateExchange()
    {
        $this->assertNull($this->exchangeManager->create($this->createTestExchangeSchema()));
    }

    /**
     * @depends testCreateWhenNotExistsThenCreateExchange
     */
    public function testCreateWhenExistsThenThrowException()
    {
        $this->exchangeManager->create($this->createTestExchangeSchema());

        $this->expectException(AMQPExceptionInterface::class);
        $this->exchangeManager->create($this->createTestExchangeSchema()->withDurable(false));
    }

    private function createTestExchangeSchema()
    {
        return AmqpExchangeSchema::newInstance()
            ->withExchangeName($this->exchangeName)
            ->withType("topic");
    }
}
