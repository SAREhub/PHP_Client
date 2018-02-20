<?php

namespace SAREhub\Client\Processor;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;

class MulticastProcessorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MulticastProcessor
     */
    private $multicastProcessor;

    protected function setUp()
    {
        $this->multicastProcessor = new MulticastProcessor();
    }

    public function testAddThenHasProcessor()
    {
        $processor = new NullProcessor();
        $this->multicastProcessor->add($processor);
        $this->assertEquals([$processor], $this->multicastProcessor->getProcessors());
    }

    public function testSetThenHasProcessor()
    {
        $processor = new NullProcessor();
        $this->multicastProcessor->set("test", $processor);
        $this->assertEquals(["test" => $processor], $this->multicastProcessor->getProcessors());
    }

    public function testRemoveWhenProcessorWithGivenIdExistsThenRemoved()
    {
        $processor = new NullProcessor();
        $this->multicastProcessor->set("test", $processor);
        $this->multicastProcessor->remove("test");
        $this->assertEquals([], $this->multicastProcessor->getProcessors());
    }

    public function testProcess()
    {
        $processor = \Mockery::mock(Processor::class);
        $this->multicastProcessor->add($processor);

        $exchange = new BasicExchange();
        $processor->expects("process")->withArgs([$exchange]);
        $this->multicastProcessor->process($exchange);
    }
}
