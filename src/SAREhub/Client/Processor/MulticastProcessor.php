<?php


namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\Exchange;

/**
 * Implements the Multicast pattern to send a message exchange to a number of processors,
 * each processor receiving a copy of the message exchange.
 */
class MulticastProcessor implements Processor
{
    /**
     * @var Processor[]
     */
    private $processors = [];

    public function process(Exchange $exchange)
    {
        foreach ($this->getProcessors() as $p) {
            $p->process($this->copyExchange($exchange));
        }
    }

    public function copyExchange(Exchange $exchange): Exchange
    {
        return BasicExchange::withIn($exchange->getIn()->copy())->setException($exchange->getException());
    }

    public function add(Processor $processor)
    {
        $this->processors[] = $processor;
    }

    public function set(string $id, Processor $processor)
    {
        $this->processors[$id] = $processor;
    }

    public function remove(string $id)
    {
        unset($this->processors[$id]);
    }

    /**
     * @return Processor[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    public function __toString()
    {
        $processors = [];
        foreach ($this->getProcessors() as $id => $p) {
            $processors[] = $id . ' => ' . $p;
        }

        return 'Multicast[ {' . implode('}, {', $processors) . '} ]';
    }
}