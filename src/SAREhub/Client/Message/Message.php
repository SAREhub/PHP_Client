<?php

namespace SAREhub\Client\Message;

interface Message
{
    /**
     * Returns copy of message
     * @return Message
     */
    public function copy(): Message;

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getHeader(string $name, $defaultValue = null);

    /**
     * Returns all message headers
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Returns true if header is sets
     * @param string $name
     * @return bool
     */
    public function hasHeader(string $name): bool;

    /**
     * Returns true if any header was sets on message
     * @return bool
     */
    public function hasAnyHeader(): bool;

    /**
     * Sets all headers to new
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers): Message;

    /**
     * Sets selected header on given value
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setHeader(string $name, $value): Message;

    /**
     * Removes selected header from message
     * @param string $name
     * @return Message
     */
    public function removeHeader(string $name): Message;

    /**
     * Returns body of message
     * @return mixed
     */
    public function getBody();

    /**
     * Returns true is message has body
     * @return bool
     */
    public function hasBody(): bool;

    /**
     * Sets new body on message
     * @param mixed $body
     * @return $this
     */
    public function setBody($body): Message;
}