<?php


namespace SAREhub\Client\Amqp;


use SAREhub\Client\Amqp\Schema\AmqpEnvironmentSchemaCreator;
use SAREhub\Commons\Misc\EnvironmentHelper;
use SAREhub\Commons\Misc\InvokableProvider;

class AmqpChannelWrapperProvider extends InvokableProvider
{
    const ENV_PREFETCH_COUNT = "AMQP_PREFETCH_COUNT";
    const DEFAULT_PREFETCH_COUNT = 20;

    /**
     * @var AmqpConnectionService
     */
    private $connectionService;

    /**
     * @var AmqpEnvironmentSchemaCreator
     */
    private $schemaCreator;

    public function __construct(AmqpConnectionService $connectionService, AmqpEnvironmentSchemaCreator $schemaCreator)
    {
        $this->connectionService = $connectionService;
        $this->schemaCreator = $schemaCreator;
    }

    public function get()
    {
        $channel = new AmqpChannelWrapper($this->schemaCreator);
        $channel->setConsumerPrefetch($this->getPrefetchCountFromEnv());
        $this->connectionService->addChannel($channel);
        return $channel;
    }

    private function getPrefetchCountFromEnv(): int
    {
        return EnvironmentHelper::getVar(self::ENV_PREFETCH_COUNT, self::DEFAULT_PREFETCH_COUNT);
    }
}