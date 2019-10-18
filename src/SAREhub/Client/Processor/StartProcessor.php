<?php


namespace SAREhub\Client\Processor;


use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\Exchange;

class StartProcessor
{
    private $processor;

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    public static function create(Processor $processor): StartProcessor
    {
        return new self($processor);
    }

    public function process($body, array $headers = []): Exchange
    {
        $exchange = BasicExchange::create($body, $headers);
        $this->processor->process($exchange);
        return $exchange;
    }
}
