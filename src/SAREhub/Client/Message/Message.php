<?php

namespace SAREhub\Client\Message;

interface Message {
	
	/**
	 * @return Message
	 */
	public function copy();
	
	/**
	 * @param $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getHeader($name, $defaultValue = null);
	
	/**
	 * @return array
	 */
	public function getHeaders();
	
	/**
	 * @param $name
	 * @return bool
	 */
	public function hasHeader($name);
	
	/**
	 * @return bool
	 */
	public function hasAnyHeader();
	
	/**
	 * Sets all headers to new.
	 * @param array $headers
	 * @return $this
	 */
	public function setHeaders(array $headers);
	
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return $this
	 */
	public function setHeader($name, $value);
	
	/**
	 * @param string $name
	 * @return $this
	 */
	public function removeHeader($name);
	
	/**
	 * @return mixed
	 */
	public function getBody();
	
	/**
	 * @return bool
	 */
	public function hasBody();
	
	/**
	 * @param mixed $body
	 * @return $this
	 */
	public function setBody($body);
}