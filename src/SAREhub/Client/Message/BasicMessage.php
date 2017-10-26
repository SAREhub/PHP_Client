<?php

namespace SAREhub\Client\Message;

/**
 * Basic implementation of Message interface
 */
class BasicMessage implements Message, \JsonSerializable
{

    protected $headers = [];
    protected $body = null;

    /**
     * @return BasicMessage
     */
    public static function newInstance()
    {
        return new self();
    }

    /**
     * @return $this
     */
    public function copy()
    {
        return self::newInstance()
            ->setHeaders($this->getHeaders())
            ->setBody($this->getBody());
    }

    public function getHeader($name, $defaultValue = null)
    {
        return $this->hasHeader($name) ? $this->headers[$name] : $defaultValue;
    }

    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasAnyHeader()
    {
        return !empty($this->headers);
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function removeHeader($name)
    {
        unset($this->headers[$name]);
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function hasBody()
    {
        return $this->body !== null;
    }

    public function setBody($body)
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
