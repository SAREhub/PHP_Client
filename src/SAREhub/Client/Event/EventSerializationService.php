<?php

namespace SAREhub\Client\Event;

/**
 * Class used for serialize events
 */
class EventSerializationService {
	
	/** @var callable[] */
	private $serializerRegistry = [];
	
	/**
	 * Register event serializer.
	 * Serializer will be invoke with event object and must return array with all event data.
	 * Attribute "type" in event data is required to reslove event type in deserialization process
	 * ```php
	 *  function (Event $event) {
	 *      ...
	 *      $eventData['type'] = $event->getEventType();
	 *      return $eventData;
	 * }
	 * ```
	 * @param string $eventType
	 * @param callable $serializer
	 */
	public function registerSerializer($eventType, callable $serializer) {
		$this->serializerRegistry[$eventType] = $serializer;
	}
	
	/**
	 * Serialize event object to json with serializer selected by event type
	 * @param Event $event
	 * @return string
	 * @throws EventSerializeException
	 */
	public function serialize(Event $event) {
		if ($serializer = $this->getSerializer($event->getEventType())) {
			if ($eventData = $serializer($event)) {
				if ($json = json_encode($eventData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) {
					return $json;
				}
				throw new EventSerializeException(
				  "json_encode error: ".json_last_error_msg(), json_last_error()
				);
			}
			throw new EventSerializeException(
			  "Serializer must return array, given: ".var_export($eventData, true)
			);
		}
		throw new EventSerializeException(
		  "Serializer for event type: ".$event->getEventType()." isn't registered"
		);
	}
	
	/**
	 * @param string $eventType
	 * @return callable|null
	 */
	public function getSerializer($eventType) {
		return $this->hasSerializer($eventType) ? $this->serializerRegistry[$eventType] : null;
	}
	
	/**
	 * @param string $eventType
	 * @return bool
	 */
	public function hasSerializer($eventType) {
		return isset($this->serializerRegistry[$eventType]);
	}
	
	
}