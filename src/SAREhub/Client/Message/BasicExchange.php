<?php

namespace SAREhub\Client\Message;

use Prophecy\Exception\Exception;

/**
 * Basic implementation of Exchange interface
 */
class BasicExchange implements Exchange, \JsonSerializable
{

    /**
     * @var Message|null
     */
    private $in = null;

    /**
     * @var Message|null
     */
    private $out = null;

    /**
     * @var \Throwable|null
     */
    private $exception = null;

    /**
     * @return BasicExchange
     */
    public static function newInstance()
    {
        return new self();
    }

    public static function withIn(Message $message)
    {
        $exchange = new self();
        $exchange->setIn($message);
        return $exchange;
    }

    public function getIn(): ?Message
    {
        return $this->in;
    }

    public function setIn(Message $message): Exchange
    {
        $this->in = $message;
        return $this;
    }

    public function getOut(): Message
    {
        if (!$this->hasOut()) {
            $this->setOut(new BasicMessage());
        }
        return $this->out;
    }

    public function setOut(Message $message): Exchange
    {
        $this->out = $message;
        return $this;
    }

    public function hasOut(): bool
    {
        return $this->out !== null;
    }

    public function clearOut(): Exchange
    {
        $this->out = null;
        return $this;
    }

    public function isFailed(): bool
    {
        return $this->exception !== null;
    }

    public function getException(): ?\Throwable
    {
        return $this->exception;
    }

    public function setException(\Throwable $e)
    {
        $this->exception = $e;
    }

    public function jsonSerialize()
    {
        return [
            "in" => $this->getIn(),
            "out" => $this->hasOut() ? $this->getOut() : null,
            "exception" => $this->isFailed() ? $this->getException() : null
        ];
    }
}

