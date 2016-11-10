<?php

namespace SAREhub\Client\Message;

interface Exchange {
	
	/**
	 * @return Message
	 */
	public function getIn();
	
	/**
	 * @param Message $message
	 * @return $this
	 */
	public function setIn(Message $message);
	
	/**
	 * Gets output message, when message isn't sets that call will discrete create it.
	 * @return Message
	 */
	public function getOut();
	
	/**
	 * @return bool
	 */
	public function hasOut();
	
	/**
	 * @param Message $message
	 * @return $this
	 */
	public function setOut(Message $message);
	
	/**
	 * Clears output message
	 * @return Exchange
	 */
	public function clearOut();
	
}