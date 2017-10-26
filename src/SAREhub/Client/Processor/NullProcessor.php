<?php

namespace SAREhub\Client\Processor;

use SAREhub\Client\Message\Exchange;

/**
 * Represents "dev/null" processor.
 */
class NullProcessor implements Processor
{

    public function process(Exchange $exchange)
    {

    }

    public function __toString()
    {
        return 'NOOP';
    }
}