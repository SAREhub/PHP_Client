<?php

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Processor\Pipeline;
use SAREhub\Client\Processor\Processor;


class PipelineTestProcessorOutSetter implements Processor
{

    public $out;
    public $lastIn;

    public function __construct()
    {
        $this->out = BasicMessage::newInstance();
    }

    public function process(Exchange $exchange)
    {
        $this->lastIn = $exchange->getIn()->copy();
        $exchange->setOut($this->out);
    }
}

class PipelineTest extends TestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * @var Pipeline
     */
    private $pipeline;

    public function setUp()
    {
        $this->pipeline = new Pipeline();
    }

    public function testAddThenProcessors()
    {
        $processor = $this->createProcessor();
        $this->pipeline->add($processor);
        $this->assertEquals([$processor], $this->pipeline->getProcessors());
    }

    public function testAddAll()
    {
        $processors = [$this->createProcessor(), $this->createProcessor()];
        $this->pipeline->addAll($processors);
        $this->assertEquals($processors, $this->pipeline->getProcessors());
    }

    public function testClear()
    {
        $this->pipeline->add($this->createProcessor())->clear();
        $this->assertEmpty($this->pipeline->getProcessors());
    }

    public function testProcessThenOrginalMessageInPreserved()
    {
        $this->pipeline->add(new PipelineTestProcessorOutSetter());
        $orginalIn = BasicMessage::newInstance()->setBody('start');
        $exchange = BasicExchange::newInstance()->setIn($orginalIn);
        $this->pipeline->process($exchange);
        $this->assertEquals($orginalIn, $exchange->getIn());
    }

    public function testProcessThenMessageOut()
    {
        $p1 = new PipelineTestProcessorOutSetter();
        $this->pipeline->add($p1);
        $exchange = $this->createExchange('start');
        $this->pipeline->process($exchange);
        $this->assertSame($p1->out, $exchange->getOut());
    }

    public function testProcessWhenProcessorSetsOutThenNextProcessorGetIt()
    {
        $p1 = new PipelineTestProcessorOutSetter();
        $p2 = $this->createProcessor();
        $this->pipeline->add($p1)->add($p2);
        $p2->expects("process")->withArgs(function (Exchange $exchange) use ($p1) {
                return $p1->out === $exchange->getIn();
        });

        $this->pipeline->process($this->createExchange());
    }

    public function testProcessWhenProcessorNotSetsOutThenNextLastIn()
    {
        $p1 = $this->createProcessor();
        $p1->shouldReceive("process");
        $p2 = $this->createProcessor();

        $this->pipeline->add($p1)->add($p2);
        $exchange = $this->createExchange();
        $orginalIn = $exchange->getIn();

        $p2->expects("process")->withArgs(function (Exchange $exchange) use ($orginalIn) {
                return $exchange->getIn() === $orginalIn;
        });

        $this->pipeline->process($exchange);
    }

    public function testProcessWhenLastProcessorNotSetsOutThenLastInIsOut()
    {
        $p1 = new PipelineTestProcessorOutSetter();
        $this->pipeline->add($p1);

        $p2 = $this->createProcessor();
        $p2->shouldReceive("process");
        $this->pipeline->add($p2);

        $exchange = $this->createExchange();
        $this->pipeline->process($exchange);
        $this->assertSame($p1->out, $exchange->getOut());
    }

    public function testProcessWhenLastProcessorNotSetsOutAndInIsOrginalThenEmptyOut()
    {
        $p1 = $this->createProcessor();
        $p1->shouldReceive("process");

        $this->pipeline->add($p1);

        $exchange = $this->createExchange();
        $this->pipeline->process($exchange);
        $this->assertFalse($exchange->hasOut());
    }

    public function testProcessWhenExchangeIsFailedAfterProcessThenStopProcessing()
    {
        $exchange = $this->createExchange();
        $p1 = $this->createProcessor();
        $p1->expects("process")->andReturnUsing(function () use ($exchange) {
            $exchange->setException(new Exception("test"));
        });
        $this->pipeline->add($p1);

        $p2 = $this->createProcessor();
        $p2->shouldNotReceive("process");
        $this->pipeline->add($p2);

        $this->pipeline->process($exchange);
    }

    public function testToString()
    {
        $this->pipeline
            ->add($this->createProcessorWithToString('processor1'))
            ->add($this->createProcessorWithToString('processor2'));

        $this->assertEquals('Pipeline[processor1 | processor2]', (string)$this->pipeline);
    }

    /**
     * @return Processor | MockInterface
     */
    private function createProcessor()
    {
        return Mockery::mock(Processor::class);
    }

    private function createProcessorWithToString($return)
    {
        $p = $this->createPartialMock(Processor::class, ['__toString', 'process']);
        $p->method('__toString')->willReturn($return);
        return $p;
    }

    /**
     * @param mixed $inBody
     * @return Exchange
     */
    private function createExchange($inBody = 'start')
    {
        return BasicExchange::newInstance()
            ->setIn(BasicMessage::newInstance()
                ->setBody($inBody)
            );
    }
}

