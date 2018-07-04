<?php

namespace SAREhub\Client\Message;

interface Exchange
{

    /**
     * @return Message
     */
    public function getIn(): Message;

    /**
     * @return mixed
     */
    public function getInBody();

    /**
     * @param Message $message
     * @return Exchange
     */
    public function setIn(Message $message): Exchange;

    /**
     * Gets output message, when message isn't sets that call will discrete create it.
     */
    public function getOut(): Message;

    /**
     * @return bool
     */
    public function hasOut(): bool;

    /**
     * @param Message $message
     * @return Exchange
     */
    public function setOut(Message $message): Exchange;

    /**
     * @return Exchange
     */
    public function clearOut(): Exchange;

    /**
     * Return true If exchange has exception sets
     * @return bool
     */
    public function isFailed(): bool;

    public function getException(): ?\Throwable;

    public function setException(?\Throwable $e): Exchange;

    /**
     * Returns copy of exchange
     * @return Exchange
     */
    public function copy(): Exchange;
}