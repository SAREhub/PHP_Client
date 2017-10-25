<?php

namespace SAREhub\Client\Util;

use Psr\Log\LoggerInterface;

interface LoggerFactory
{

    /**
     * @param string $name
     * @return LoggerInterface
     */
    public function create($name);
}