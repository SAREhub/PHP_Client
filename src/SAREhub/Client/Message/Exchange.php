<?php

namespace SAREhub\Client\Message;

interface Exchange
{
    public function getIn(): ?Message;

    public function setIn(Message $message): Exchange;

    /**
     * Gets output message, when message isn't sets that call will discrete create it.
     */
    public function getOut(): Message;

    public function hasOut(): bool;

    public function setOut(Message $message): Exchange;

    public function clearOut(): Exchange;

    /**
     * Return true If exchange has exception sets
     * @return bool
     */
    public function isFailed(): bool;

    public function getException(): ?\Throwable;

    public function setException(?\Throwable $e): Exchange;


}