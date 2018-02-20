<?php


namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;

class MulticastProcessor implements Processor
{
    /**
     * @var Processor[]
     */
    private $processors = [];

    public function process(Exchange $exchange)
    {
        foreach ($this->getProcessors() as $p) {
            $p->process($exchange);
        }
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