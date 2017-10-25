<?php

namespace SAREhub\Client\Event;

use SAREhub\Client\User\User;

/**
 * Base event class
 */
interface Event
{

    /**
     * Returns event type id
     * @return string
     */
    public function getType();

    /**
     * Returns event type name
     * @deprecated Use getType
     * @return string
     */
    public function getEventType();

    /**
     * @return int
     */
    public function getTime();

    /**
     * @return User
     */
    public function getUser();

    /**
     * @return bool
     */
    public function hasUser();

    /**
     * @param string $name
     * @return mixed
     */
    public function getProperty($name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasProperty($name);

    /**
     * @return array
     */
    public function getProperties();
}