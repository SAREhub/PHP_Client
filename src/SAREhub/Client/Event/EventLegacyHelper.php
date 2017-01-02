<?php

namespace SAREhub\Client\Event;

// remove after add sequence id
class EventLegacyHelper {
	
	const EXTRA = 'extra';
	const START_PARAMS = 'start_params';
	
	public static function copyFromEventToEvent(BasicEvent $input, BasicEvent $output) {
		if ($input->hasProperty(self::EXTRA)) {
			$output->withProperty(self::EXTRA, $input->getProperty(self::EXTRA));
		}
		
		if ($input->hasProperty(self::START_PARAMS)) {
			$output->withProperty(self::START_PARAMS, $input->getProperty(self::START_PARAMS));
		}
	}
	
	public static function copyFromDataToEvent(array $eventData, BasicEvent $event) {
		if (isset($eventData[self::EXTRA])) {
			$event->withProperty(self::EXTRA, $eventData[self::EXTRA]);
		}
		
		if (isset($eventData[self::START_PARAMS])) {
			$event->withProperty(self::START_PARAMS, $eventData[self::START_PARAMS]);
		}
	}
	
	public static function copyFromEventToEventData(BasicEvent $event, array &$eventData) {
		if ($event->hasProperty(self::EXTRA)) {
			$eventData[self::EXTRA] = $event->getProperty(self::EXTRA);
		}
		
		if ($event->hasProperty(self::START_PARAMS)) {
			$eventData[self::START_PARAMS] = $event->getProperty(self::START_PARAMS);
		}
		
		$properties = $event->getProperties();
		unset($properties[self::EXTRA]);
		unset($properties[self::START_PARAMS]);
		
		$event->withProperties($properties);
	}
}