<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Message\Message;

class ExchangeCatcherProcessor implements Processor
{
    /**
     * @var Exchange[]
     */
    private $caughtHistory = [];

    public function process(Exchange $exchange)
    {
        $this->caughtHistory[] = $exchange->copy();
    }

    /**
     * @return Exchange[]
     */
    public function getCaughtHistory(): array
    {
        return $this->caughtHistory;
    }

    /**
     * @return Message[]
     */
    public function getCaughtInMessages(): array
    {
        $history = [];
        foreach ($this->getCaughtHistory() as $exchange) {
            $history[] = $exchange->getIn();
        }
        return $history;
    }
}