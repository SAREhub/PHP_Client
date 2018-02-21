<?php

namespace SAREhub\Client\Processor;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Exchange;

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

    public function testProcessThenProcessingExchangeIsNotOrginal()
    {
        $processor = \Mockery::mock(Processor::class);
        $this->multicastProcessor->add($processor);

        $orginalExchange = new BasicExchange();
        $processor->expects("process")->withArgs(function (Exchange $exchange) use ($orginalExchange) {
            return $exchange !== $orginalExchange;
        });

        $this->multicastProcessor->process($orginalExchange);
    }

    public function testProcessThenProcessingExchangeHasCopyOfInMessage()
    {
        $processor = \Mockery::mock(Processor::class);
        $this->multicastProcessor->add($processor);

        $orginalIn = BasicMessage::newInstance();
        $orginalExchange = BasicExchange::newInstance()->setIn($orginalIn);
        $processor->expects("process")->withArgs(function (Exchange $exchange) use ($orginalIn) {
            return $exchange->getIn() !== $orginalIn;
        });

        $this->multicastProcessor->process($orginalExchange);
    }

    public function testProcessThenProcessingExchangeHasSameOrginalExchangeException()
    {
        $processor = \Mockery::mock(Processor::class);
        $this->multicastProcessor->add($processor);

        $orginalException = new \Exception();
        $orginalExchange = BasicExchange::newInstance()->setException($orginalException);
        $processor->expects("process")->withArgs(function (Exchange $exchange) use ($orginalException) {
            return $exchange->getException() === $orginalException;
        });

        $this->multicastProcessor->process($orginalExchange);
    }
}
