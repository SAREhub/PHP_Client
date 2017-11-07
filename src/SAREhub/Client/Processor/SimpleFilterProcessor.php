<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;

class SimpleFilterProcessor implements Processor
{

    /**
     * @var callable
     */
    private $predicate;

    /**
     * @var Processor
     */
    private $to;

    public function __construct(callable $predicate)
    {
        $this->predicate = $predicate;
    }

    public static function newWithPredicate(callable $predicate)
    {
        return new self($predicate);
    }

    public function to(Processor $to): SimpleFilterProcessor
    {
        $this->to = $to;
        return $this;
    }

    public function process(Exchange $exchange)
    {
        if (($this->predicate)($exchange)) {
            $this->getTo()->process($exchange);
        }
    }

    public function getPredicate(): callable
    {
        return $this->predicate;
    }


    public function getTo(): Processor
    {
        return $this->to;
    }

    public function __toString()
    {
        $predicateInfo = method_exists($this->getPredicate(), '__toString') ? (string)$this->predicate : '*closure*';
        return 'SimpleFilterProcessor[' . $predicateInfo . '? ' . $this->getOnPass() . ']';
    }
}