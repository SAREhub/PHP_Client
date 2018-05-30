<?php


namespace SAREhub\Client\Processor;


use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Message\Message;

interface SplittingStrategy
{
    /**
     * @param Message $message
     * @return Exchange[]
     */
    public function split(Message $message): array;

}