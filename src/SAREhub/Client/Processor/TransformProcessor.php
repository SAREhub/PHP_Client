<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Util\IdAware;

/**
 * That processor execute transform function on exchange.
 */
class TransformProcessor implements Processor, IdAware
{

    private $id = '';
    private $transformer;

    public function __construct(callable $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param callable $transformer
     * @return TransformProcessor
     */
    public static function transform(callable $transformer)
    {
        return new self($transformer);
    }

    public function process(Exchange $exchange)
    {
        $c = $this->transformer;
        $c($exchange);
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param String $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function __toString()
    {
        return 'Transform[' . $this->getId() . ']';
    }
}