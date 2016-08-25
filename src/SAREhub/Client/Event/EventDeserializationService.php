<?php

namespace SAREhub\Client\Event;

interface EventDeserializationService {
	/**
	 * Register event deserializer. Deserializer must be function with one array type argument
	 * ```php
	 *  function (array $eventData) {
	 *      ...
	 *      return new TypeEvent();
	 * }
	 * ```
	 * @param string $eventType
	 * @param callable $deserializer
	 */
	public function registerDeserializer($eventType, callable $deserializer);
	
	/**
	 * Deserialize event data string in json format
	 * ```json
	 *  {
	 *      "type": "eventType"
	 *  }
	 * ```
	 * @param string $eventData
	 * @return Event
	 * @throws EventDeserializeException
	 */
	public function deserialize($eventData);
	
	/**
	 * @param string $eventType
	 * @return callable|null
	 */
	public function getDeserializer($eventType);
	
	/**
	 * @param string $eventType
	 * @return bool
	 */
	public function hasDeserializer($eventType);
}