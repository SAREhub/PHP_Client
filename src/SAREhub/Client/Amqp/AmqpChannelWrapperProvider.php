<?php


namespace SAREhub\Client\Amqp;


use SAREhub\Client\Amqp\Schema\AmqpEnvironmentSchemaCreator;
use SAREhub\Commons\Misc\EnvironmentHelper;
use SAREhub\Commons\Misc\InvokableProvider;

class AmqpChannelWrapperProvider extends InvokableProvider
{
    const ENV_PREFETCH_COUNT = "AMQP_PREFETCH_COUNT";
    const DEFAULT_PREFETCH_COUNT = 20;

    const DEFAULT_CHANNEL_ID = "MAIN";

    /**
     * @var AmqpConnectionService
     */
    private $connectionService;

    /**
     * @var string
     */
    private $channelId;

    /**
     * @var AmqpEnvironmentSchemaCreator
     */
    private $schemaCreator;

    public function __construct(
        AmqpConnectionService $connectionService,
        AmqpEnvironmentSchemaCreator $schemaCreator,
        string $channelId = self::DEFAULT_CHANNEL_ID
    )
    {
        $this->connectionService = $connectionService;
        $this->schemaCreator = $schemaCreator;
        $this->channelId = $channelId;
    }

    public function get()
    {
        $wrapper = $this->connectionService->createChannel($this->channelId, $this->schemaCreator);
        $wrapper->setConsumerPrefetch($this->getPrefetchCountFromEnv());
        return $wrapper;
    }

    private function getPrefetchCountFromEnv(): int
    {
        return EnvironmentHelper::getVar(self::ENV_PREFETCH_COUNT, self::DEFAULT_PREFETCH_COUNT);
    }
}