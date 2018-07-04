<?php

namespace SAREhub\Client\Message;

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
     * @param null|Message $in
     */
    public function __construct(?Message $in = null)
    {
        $this->in = $in ?? BasicMessage::newInstance();
    }

    public static function newInstance(): Exchange
    {
        return new self();
    }

    public static function withIn(Message $message): Exchange
    {
        return new self($message);
    }

    public function getIn(): Message
    {
        return $this->in;
    }

    public function setIn(Message $message): Exchange
    {
        $this->in = $message;
        return $this;
    }

    public function getInBody()
    {
        $this->getIn()->getBody();
    }

    public function getOut(): Message
    {
        if (!$this->hasOut()) {
            $this->setOut(BasicMessage::newInstance());
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

    public function setException(?\Throwable $e): Exchange
    {
        $this->exception = $e;
        return $this;
    }

    public function copy(): Exchange
    {
        $copy = self::withIn($this->getIn()->copy());
        if ($this->hasOut()) {
            $copy->setOut($this->getOut()->copy());
        }
        return $copy;
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
