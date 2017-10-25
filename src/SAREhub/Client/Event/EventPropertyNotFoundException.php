<?php

namespace SAREhub\Client\Event;


class EventPropertyNotFoundException extends \RuntimeException
{

    private $event;
    private $property;

    public function __construct(Event $event, $property, \Exception $previous = null)
    {
        parent::__construct('property ' . $property . " not found in event: " . var_export($event, true), 0, $previous);
        $this->event = $event;
        $this->property = $property;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getProperty()
    {
        return $this->property;
    }
}