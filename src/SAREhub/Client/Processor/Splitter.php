<?php


namespace SAREhub\Client\Processor;


use SAREhub\Client\Message\Exchange;

/**
 * Use a Splitter to break out the composite message into a series of individual messages,
 * each containing data related to one item.
 * http://www.enterpriseintegrationpatterns.com/patterns/messaging/Sequencer.html
 */
class Splitter implements Processor
{
    /**
     * @var SplittingStrategy
     */
    private $splittingStrategy;

    /**
     * @var Processor;
     */
    private $partProcessor;

    public function __construct(SplittingStrategy $splittingStrategy, Processor $partProcessor)
    {
        $this->splittingStrategy = $splittingStrategy;
        $this->partProcessor = $partProcessor;
    }

    public function process(Exchange $exchange)
    {
        $partList = $this->splittingStrategy->split($exchange->getIn());
        foreach ($partList as $part) {
            $this->partProcessor->process($part);
        }
    }
}