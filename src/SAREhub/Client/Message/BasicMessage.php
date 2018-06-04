<?php

namespace SAREhub\Client\Message;

/**
 * Basic implementation of Message interface
 */
class BasicMessage implements Message, \JsonSerializable
{

    /**
     * @var array
     */
    private $headers;

    /**
     * @var mixed
     */
    private $body;

    public function __construct(array $headers = [], $body = null)
    {
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @param mixed $body
     * @return Message
     */
    public static function withBody($body): Message
    {
        return new self([], $body);
    }

    public static function newInstance(): Message
    {
        return new self();
    }

    /**
     * @return $this
     */
    public function copy(): Message
    {
        return self::newInstance()
            ->setHeaders($this->getHeaders())
            ->setBody($this->getBody());
    }

    public function getHeader(string $name, $defaultValue = null)
    {
        return $this->hasHeader($name) ? $this->headers[$name] : $defaultValue;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasAnyHeader(): bool
    {
        return !empty($this->headers);
    }

    public function setHeaders(array $headers): Message
    {
        $this->headers = $headers;
        return $this;
    }

    public function setHeader(string $name, $value): Message
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function removeHeader(string $name): Message
    {
        unset($this->headers[$name]);
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function hasBody(): bool
    {
        return $this->body !== null;
    }

    public function setBody($body): Message
    {
        $this->body = $body;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            "headers" => $this->getHeaders(),
            "body" => $this->getBody()
        ];
    }
}
