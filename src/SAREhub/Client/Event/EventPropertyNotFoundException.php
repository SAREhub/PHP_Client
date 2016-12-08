<?php

namespace SAREhub\Client\Event;


class EventPropertyNotFoundException extends \RuntimeException {
	
	private $event;
	private $attributeName;
	
	public function __construct(Event $event, $attributeName, \Exception $previous = null) {
		parent::__construct('attribute '.$attributeName." not found in event: ".var_export($event, true), 0, $previous);
	}
	
	public function getEvent() {
		return $this->event;
	}
	
	public function getAttributeName() {
		return $this->attributeName;
	}
}