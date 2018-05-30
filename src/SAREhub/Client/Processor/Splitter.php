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
     * @var PartProcessor;
     */
    private $partProcessor;


    public function process(Exchange $exchange)
    {

    }
}