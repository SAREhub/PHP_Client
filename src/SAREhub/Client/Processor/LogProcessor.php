<?php


namespace SAREhub\Client\Processor;


use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Util\IdAware;

class LogProcessor implements Processor, LoggerAwareInterface, IdAware
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    private $id;

    private $logLevel = "debug";

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setLogLevel(string $logLevel)
    {
        $this->logLevel = $logLevel;
        return $this;
    }

    public function process(Exchange $exchange)
    {
        $this->logger->log($this->logLevel, (string)$this, [
            "exchange" => $exchange
        ]);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function __toString()
    {
        return 'LogProcessor[' . $this->getId() . ']';
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}