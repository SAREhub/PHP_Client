<?php

namespace SAREhub\Client\Event;


use SAREhub\Client\Message\Message;

class EventHelper {
	
	/**
	 * @param Message $message
	 * @return Event
	 */
	public static function extract(Message $message) {
		return $message->getBody();
	}
}