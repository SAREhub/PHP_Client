<?php

namespace SAREhub\Client\Amqp;


/**
 * Routing key is string in format: part1[.partN]
 */
class RoutingKey implements \IteratorAggregate
{

    /** @var array */
    protected $parts;

    /**
     * Defaults create empty routing key.
     * String will be converted to array(explode by dot).
     * Array of routing key parts.
     * @param string|array|null $routingKey
     */
    public function __construct($routingKey = null)
    {
        $routingKey = ($routingKey === null) ? [] : $routingKey;
        $this->parts = is_array($routingKey) ? $routingKey : explode('.', $routingKey);
    }

    /**
     * @param string $routingKey
     * @return RoutingKey
     */
    public static function createFromString($routingKey)
    {
        return new self($routingKey);
    }

    /**
     * @param string part
     * @return $this
     */
    public function addPart($part)
    {
        $this->parts[] = $part;
        return $this;
    }

    /**
     * @param int index
     * @return string
     */
    public function getPart($index)
    {
        return isset($this->parts[$index]) ? $this->parts[$index] : '';
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->parts);
    }

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }

    public function getIterator()
    {
        return $this->parts;
    }

    public function __toString()
    {
        return implode('.', $this->parts);
    }

}