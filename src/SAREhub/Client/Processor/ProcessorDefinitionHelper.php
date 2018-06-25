<?php
namespace SAREhub\Client\Processor;

use DI\Definition\Helper\CreateDefinitionHelper;
use function DI\create;

class ProcessorDefinitionHelper
{
    public static function filter(callable $predicate, $to)
    {
        return create(SimpleFilterProcessor::class)
            ->constructor(self::closureValue($predicate))->method("to", $to);
    }

    public static function unmarshal($dataFormat): CreateDefinitionHelper
    {
        return create(UnmarshalProcessor::class)->constructor($dataFormat);
    }

    public static function marshal($dataFormat): CreateDefinitionHelper
    {
        return create(MarshalProcessor::class)->constructor($dataFormat);
    }

    public static function pipeline(array $processors): CreateDefinitionHelper
    {
        return create(Pipeline::class)->method("addAll", $processors);
    }

    public static function transform(callable $transformer): CreateDefinitionHelper
    {
        return create(TransformProcessor::class)->constructor(self::closureValue($transformer));
    }

    public static function multicast(array $processors): CreateDefinitionHelper
    {
        $multicast = create(MulticastProcessor::class);
        foreach ($processors as $processor) {
            $multicast->method("add", $processor);
        }
        return $multicast;
    }

    public static function headerAppender(array $headers): CreateDefinitionHelper
    {
        return create(HeaderAppenderProcessor::class)->method("withHeaders", $headers);
    }

    public static function logProcessor($logger): CreateDefinitionHelper
    {
        return create(LogProcessor::class)->constructor($logger);
    }

    public static function blackhole(): CreateDefinitionHelper
    {
        return create(NullProcessor::class);
    }

    /**
     * @param callable $routingFunction
     * @param array $routes
     * @tutorial
     * $routes = [
     *      "routingKey" => Processor
     * ]
     * @return CreateDefinitionHelper
     */
    public static function router(callable $routingFunction, array $routes): CreateDefinitionHelper
    {
        $router = create(Router::class)->constructor(self::closureValue($routingFunction));
        foreach ($routes as $routingKey => $processor) {
            $router->method("addRoute", $routingKey, $processor);
        }
        return $router;
    }

    public static function closureValue(\Closure $closure): \Closure
    {
        return function () use ($closure) {
            return $closure;
        };
    }
}