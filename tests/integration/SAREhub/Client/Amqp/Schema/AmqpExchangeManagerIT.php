<?php

namespace SAREhub\Client\Amqp\Schema;


use SAREhub\Client\Amqp\AmqpTestCase;

class AmqpExchangeManagerIT extends AmqpTestCase
{
    /**
     * @var AmqpExchangeManager
     */
    private $exchangeManager;

    protected function setUp()
    {
        parent::setUp();
        $this->exchangeManager = new AmqpExchangeManager();
    }

    /**
     * @throws AmqpSchemaException
     */
    public function testCreate()
    {
        $schema = AmqpExchangeSchema::newInstance()
            ->withName("test_exchange")
            ->withAutoDelete(true)
            ->withDurable(false)
            ->withType("topic");
        $this->assertTrue($this->exchangeManager->create($schema, $this->channel));
    }

    /**
     * @throws AmqpSchemaException
     */
    public function testBindToExchange()
    {
        $this->createExchange("exchange_1");
        $this->createExchange("exchange_2");

        $bindingSchema = AmqpExchangeBindingSchema::newInstance()
            ->withDestination("exchange_2")
            ->withSource("exchange_1")
            ->withRoutingKey("a.b");

        $this->assertTrue($this->exchangeManager->bindToExchange($bindingSchema, $this->channel));
    }

    /**
     * @param string $name
     * @throws AmqpSchemaException
     */
    private function createExchange(string $name)
    {
        $schema = AmqpExchangeSchema::newInstance()
            ->withName($name)
            ->withAutoDelete(true);
        $this->exchangeManager->create($schema, $this->channel);
    }
}
