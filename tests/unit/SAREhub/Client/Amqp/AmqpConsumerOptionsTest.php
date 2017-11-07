<?php

namespace SAREhub\Client\Amqp;

use PHPUnit\Framework\TestCase;

class AmqpConsumerOptionsTest extends TestCase
{
    /**
     * @var AmqpConsumerOptions
     */
    private $options;

    protected function setUp()
    {
        $this->options = AmqpConsumerOptions::newInstance();
    }

    public function testGetConsumeArgumentsWhenPrioritySets()
    {
        $args = $this->options->setPriority(1)->getConsumeArguments();
        $this->assertEquals(["x-priority" => 1], $args->getNativeData());
    }

    public function testGetConsumeArgumentsWhenPriorityNotSets()
    {
        $args = $this->options->getConsumeArguments();
        $this->assertEquals([], $args->getNativeData());
    }
}
