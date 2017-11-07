<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;

class Pipeline implements Processor
{

    /**
     * @var Processor[]
     */
    private $processors = [];

    /**
     * @return Pipeline
     */
    public static function newInstance()
    {
        return new self();
    }

    public function process(Exchange $exchange)
    {
        $currentExchange = $exchange;
        $orginalMessage = $currentExchange->getIn();

        $isFirstTime = true;
        foreach ($this->getProcessors() as $processor) {
            if ($isFirstTime) {
                $isFirstTime = false;
            } else {
                $currentExchange = $this->createNextExchange($currentExchange);
            }

            $processor->process($currentExchange);
            if ($exchange->isFailed()) {
                break;
            }
        }

        if (!$currentExchange->hasOut() && $currentExchange->getIn() !== $orginalMessage) {
            $currentExchange->setOut($currentExchange->getIn());
        }

        $currentExchange->setIn($orginalMessage);
    }

    protected function createNextExchange(Exchange $previousExchange)
    {
        if ($previousExchange->hasOut()) {
            $out = $previousExchange->getOut();
            $previousExchange->clearOut();
            $previousExchange->setIn($out);
        }
        return $previousExchange;
    }

    public function add(Processor $processor): Pipeline
    {
        $this->processors[] = $processor;
        return $this;
    }

    public function addAll(array $processors): Pipeline
    {
        foreach ($processors as $processor) {
            $this->add($processor);
        }

        return $this;
    }

    public function clear(): Pipeline
    {
        $this->processors = [];
        return $this;
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
        return "Pipeline[" . implode(" | ", $this->getProcessors()) . "]";
    }


}