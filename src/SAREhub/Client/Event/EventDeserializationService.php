<?php

namespace SAREhub\Client\Event;

/**
 * Class used for deserialize events
 */
class EventDeserializationService {
	
	/** @var callable[] */
	private $deserializerRegistry = [];
	
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
	public function registerDeserializer($eventType, callable $deserializer) {
		$this->deserializerRegistry[$eventType] = $deserializer;
	}
	
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
	public function deserialize($eventData) {
		if (!($decodedEventData = json_decode($eventData, true))) {
			throw new EventDeserializeException(
			  "Can't decode json: ".json_last_error_msg()."\nData: ".$eventData, json_last_error()
			);
		}
		
		if ($deserializer = $this->getDeserializer($decodedEventData['type'])) {
			if (($event = $deserializer($decodedEventData)) instanceof Event) {
				return $event;
			}
			throw new EventDeserializeException(
			  "Deserializer for event type: ".$decodedEventData['type']." must return object instanceof Event"
			);
		}
		throw new EventDeserializeException(
		  "Deserializer for event type: ".$decodedEventData['type']." isn't registered"
		);
	}
	
	/**
	 * @param string $eventType
	 * @return callable|null
	 */
	public function getDeserializer($eventType) {
		return $this->hasDeserializer($eventType) ? $this->deserializerRegistry[$eventType] : null;
	}
	
	/**
	 * @param string $eventType
	 * @return bool
	 */
	public function hasDeserializer($eventType) {
		return isset($this->deserializerRegistry[$eventType]);
	}
}