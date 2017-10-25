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
    private $onPass;

    /**
     * @return SimpleFilterProcessor
     */
    public static function newInstance()
    {
        return new self();
    }

    /**
     * @param callable $predicate
     * @return $this
     */
    public function withPredicate(callable $predicate)
    {
        $this->predicate = $predicate;
        return $this;
    }

    /**
     * @param Processor $onPass
     * @return $this
     */
    public function withOnPass(Processor $onPass)
    {
        $this->onPass = $onPass;
        return $this;
    }

    public function process(Exchange $exchange)
    {
        $p = $this->getPredicate();
        if ($p($exchange)) {
            $this->getOnPass()->process($exchange);
        }
    }

    /**
     * @return callable
     */
    public function getPredicate()
    {
        return $this->predicate;
    }

    /**
     * @return Processor
     */
    public function getOnPass()
    {
        return $this->onPass;
    }

    public function __toString()
    {
        $predicateInfo = method_exists($this->getPredicate(), '__toString') ? (string)$this->predicate : '*closure*';
        return 'SimpleFilterProcessor[' . $predicateInfo . '? ' . $this->getOnPass() . ']';
    }
}