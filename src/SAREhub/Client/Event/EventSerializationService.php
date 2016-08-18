<?php
namespace SAREhub\Client\Event;

interface EventSerializationService {
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
	public function registerSerializer($eventType, callable $serializer);
	
	/**
	 * Serialize event object to json with serializer selected by event type
	 * @param Event $event
	 * @return string
	 * @throws EventSerializeException
	 */
	public function serialize(Event $event);
	
	/**
	 * @param string $eventType
	 * @return callable|null
	 */
	public function getSerializer($eventType);
	
	/**
	 * @param string $eventType
	 * @return bool
	 */
	public function hasSerializer($eventType);
}