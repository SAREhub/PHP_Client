<?php


namespace SAREhub\Client\Processor;


use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
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

    public function process(Exchange $exchange)
    {
        $this->logger->log($this->logLevel, (string)$this, [
            "exchange" => $exchange
        ]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    public function setLogLevel(string $logLevel): LogProcessor
    {
        $this->isValidLogLevel($logLevel);
        $this->logLevel = $logLevel;
        return $this;
    }

    private function isValidLogLevel(string $logLevel): void
    {
        $logLevelReflection = new \ReflectionClass(LogLevel::class);
        if (!in_array(strtolower($logLevel), $logLevelReflection->getConstants())) {
            throw new \InvalidArgumentException("invalid LogLevel: $logLevel");
        }
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __toString()
    {
        return sprintf('LogProcessor[id=%s,loglevel=%s]', $this->getId(), $this->getLogLevel());
    }

}