<?php
namespace SAREhub\Client\Event;

use SAREhub\Client\DataFormat\DataFormat;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\User\User;

class RawEventDataFormat implements DataFormat {
	
	public function marshal(Exchange $exchange) {
		$event = EventHelper::extract($exchange->getIn());
		$eventData = [
		  'type' => $event->getEventType(),
		  'time' => $event->getTime()
		];
		
		if ($event->hasUser()) {
			$eventData['user'] = $event->getUser()->getKeys();
		}
		
		EventLegacyHelper::copyFromEventToEventData($event, $eventData);
		$eventData['params'] = $event->getProperties();
		
		$exchange->getOut()->setBody($eventData);
	}
	
	/**
	 * @param Exchange $exchange
	 */
	public function unmarshal(Exchange $exchange) {
		$eventData = $exchange->getIn()->getBody();
		$event = BasicEvent::newInstanceOf($eventData['type'])
		  ->withTime($eventData['time'])
		  ->withProperties($eventData['params']);
		
		if (isset($eventData['user'])) {
			$event->withUser(new User($eventData['user']));
		}
		
		EventLegacyHelper::copyFromDataToEvent($eventData, $event);
		$exchange->getOut()->setBody($event);
	}
	
	public function __toString() {
		return 'DataFormat[RawEvent]';
	}
}