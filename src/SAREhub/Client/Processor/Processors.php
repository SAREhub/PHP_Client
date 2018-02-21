<?php

namespace SAREhub\Client\Processor;

use Psr\Log\LoggerInterface;
use SAREhub\Client\DataFormat\DataFormat;

class Processors
{
    public static function blackhole(): NullProcessor
    {
        return new NullProcessor();
    }

    public static function log(LoggerInterface $logger): LogProcessor
    {
        return new LogProcessor($logger);
    }

    public static function transform(callable $transformer): TransformProcessor
    {
        return new TransformProcessor($transformer);
    }

    public static function pipeline(): Pipeline
    {
        return new Pipeline();
    }

    public static function multicast(): MulticastProcessor
    {
        return new MulticastProcessor();
    }

    public static function router(callable $routingFunction): Router
    {
        return Router::withRoutingFunction($routingFunction);
    }

    public static function marshal(DataFormat $format): MarshalProcessor
    {
        return new MarshalProcessor($format);
    }

    public static function unmarshal(DataFormat $format): UnmarshalProcessor
    {
        return new UnmarshalProcessor($format);
    }

    public static function filter(callable $predicate): SimpleFilterProcessor
    {
        return new SimpleFilterProcessor($predicate);
    }


}