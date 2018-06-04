<?php


namespace SAREhub\Client\Processor;


use SAREhub\Client\Message\Message;

/**
 * Interface to implement strategy used in Splitter to split input Message to parts.
 * http://www.enterpriseintegrationpatterns.com/patterns/messaging/Sequencer.html
 */
interface SplittingStrategy
{

    /**
     * @param Message $message
     * @return iterable Returns iterable list of exchanges with input message parts
     */
    public function split(Message $message): iterable;
}