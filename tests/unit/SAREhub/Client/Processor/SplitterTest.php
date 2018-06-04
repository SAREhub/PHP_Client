<?php

namespace SAREhub\Client\Processor;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class SplitterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MockInterface | SplittingStrategy
     */
    private $strategy;

    /**
     * @var MockInterface | Processor
     */
    private $partProcessor;

    /**
     * @var Splitter
     */
    private $splitter;

    public function setUp()
    {
        $this->partProcessor = \Mockery::mock(Processor::class);
        $this->strategy = \Mockery::mock(SplittingStrategy::class);
        $this->splitter = new Splitter($this->strategy, $this->partProcessor);
    }

    public function testProcess()
    {
        $exchange = BasicExchange::withIn(BasicMessage::newInstance());
        $splitsExchange = BasicExchange::newInstance();

        $this->strategy->expects("split")->withArgs([$exchange->getIn()])->andReturn([$splitsExchange]);
        $this->partProcessor->expects("process")->withArgs([$splitsExchange]);

        $this->splitter->process($exchange);
    }

    /**
     * @return MockInterface | Processor
     */
    protected function createProcessor(): Processor
    {
        return $partProcessor = \Mockery::mock(Processor::class);
    }
}
