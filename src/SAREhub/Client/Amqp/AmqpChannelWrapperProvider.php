<?php


namespace SAREhub\Service\Throttler\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;
use SAREhub\Client\Amqp\AmqpChannelWrapper;
use SAREhub\Commons\Misc\EnvironmentHelper;
use SAREhub\Commons\Misc\InvokableProvider;

class AmqpChannelWrapperProvider extends InvokableProvider
{
    const ENV_PREFETCH_COUNT = "AMQP_PREFETCH_COUNT";
    const DEFAULT_PREFETCH_COUNT = 5;

    /**
     * @var AMQPChannel
     */
    private $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    public function get()
    {
        $wrapper = new AmqpChannelWrapper($this->channel);
        $wrapper->setPrefetchCountPerConsumer($this->getPrefetchCountFromEnv());
        return $wrapper;
    }

    private function getPrefetchCountFromEnv(): int
    {
        return EnvironmentHelper::getVar(self::ENV_PREFETCH_COUNT, self::DEFAULT_PREFETCH_COUNT);
    }
}