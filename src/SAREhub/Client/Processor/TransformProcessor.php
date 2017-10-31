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


    public static function transform(callable $transformer): TransformProcessor
    {
        return new self($transformer);
    }

    public function process(Exchange $exchange)
    {
        ($this->transformer)($exchange);
    }

    public function getTransformer(): callable
    {
        return $this->transformer;
    }

    public function getId()
    {
        return $this->id;
    }

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