<?php

namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;
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

    /**
     * @throws AmqpSchemaException
     */
    public function testCreateWhenNotExistsThenCreateExchange()
    {
        $this->assertTrue($this->exchangeManager->create($this->createTestExchangeSchema()));
    }

    /**
     * @depends testCreateWhenNotExistsThenCreateExchange
     * @throws AmqpSchemaException
     */
    public function testCreateWhenExistsThenThrowException()
    {
        $this->exchangeManager->create($this->createTestExchangeSchema());

        $this->expectException(AmqpSchemaException::class);

        $this->exchangeManager->create($this->createTestExchangeSchema()->withDurable(false));
    }

    private function createTestExchangeSchema()
    {
        return AmqpExchangeSchema::newInstance()
            ->withName($this->exchangeName)
            ->withType("topic");
    }
}
