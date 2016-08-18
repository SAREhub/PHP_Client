<?php

namespace SAREhub\Client\Event;

class JsonEventSerializationService implements EventSerializationService {
	
	/** @var callable[] */
	private $serializerRegistry = [];
	
	public function registerSerializer($eventType, callable $serializer) {
		$this->serializerRegistry[$eventType] = $serializer;
	}
	
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
	
	public function getSerializer($eventType) {
		return $this->hasSerializer($eventType) ? $this->serializerRegistry[$eventType] : null;
	}
	
	public function hasSerializer($eventType) {
		return isset($this->serializerRegistry[$eventType]);
	}
}
