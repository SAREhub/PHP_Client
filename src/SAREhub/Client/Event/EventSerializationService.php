<?php

namespace SAREhub\Client\Event;

/**
 * Class used for serialize and deserialize events
 */
class EventSerializationService {
	
	/** @var callable[] */
	private $serializerRegistry = [];
	
	/** @var callable[] */
	private $deserializerRegistry = [];
	
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