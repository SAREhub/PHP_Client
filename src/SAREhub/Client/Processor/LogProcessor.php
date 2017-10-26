<?php


namespace SAREhub\Client\Processor;


use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SAREhub\Client\Message\Exchange;

class LogProcessor implements Processor,LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function process(Exchange $exchange)
    {
        $this->logger->info($exchange);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}