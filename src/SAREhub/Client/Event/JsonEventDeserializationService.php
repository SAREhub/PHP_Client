<?php

namespace SAREhub\Client\Event;

class JsonEventDeserializationService implements EventDeserializationService {
	
	/** @var callable[] */
	private $deserializerRegistry = [];
	
	public function registerDeserializer($eventType, callable $deserializer) {
		$this->deserializerRegistry[$eventType] = $deserializer;
	}
	
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
	
	public function getDeserializer($eventType) {
		return $this->hasDeserializer($eventType) ? $this->deserializerRegistry[$eventType] : null;
	}
	
	public function hasDeserializer($eventType) {
		return isset($this->deserializerRegistry[$eventType]);
	}
}
