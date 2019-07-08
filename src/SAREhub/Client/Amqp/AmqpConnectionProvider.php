<?php


namespace SAREhub\Client\Amqp;


use SAREhub\Commons\Misc\EnvironmentHelper;
use SAREhub\Commons\Misc\InvokableProvider;
use SAREhub\Commons\Misc\RetryFunctionWrapper;

class AmqpConnectionProvider extends InvokableProvider
{
    const ENV_MAX_RETRIES = "AMQP_CONNECT_MAX_RETRIES";
    const DEFAULT_MAX_RETRIES = 3;
    const ENV_INITIAL_WAIT = "AMQP_CONNECT_INITIAL_WAIT";
    const DEFAULT_INITIAL_WAIT = 3.0;
    const ENV_EXPONENT = "AMQP_CONNECT_EXPONENT";
    const DEFAULT_EXPONENT = 2;

    /**
     * @var AmqpConnectionFactory
     */
    private $connectionFactory;

    /**
     * @var AmqpConnectionOptions
     */
    private $options;

    public function __construct(AmqpConnectionFactory $factory, AmqpConnectionOptions $options)
    {
        $this->connectionFactory = $factory;
        $this->options = $options;
    }

    public function get()
    {
        $createCallback = function () {
            return $this->connectionFactory->create($this->options);
        };
        $maxRetries = (int)EnvironmentHelper::getVar(self::ENV_MAX_RETRIES, self::DEFAULT_MAX_RETRIES);
        $initialWait = (float)EnvironmentHelper::getVar(self::ENV_INITIAL_WAIT, self::DEFAULT_INITIAL_WAIT);
        $exponent = (int)EnvironmentHelper::getVar(self::ENV_EXPONENT, self::DEFAULT_EXPONENT);
        $retryWrapper = new RetryFunctionWrapper(
            $createCallback,
            [],//[\Exception::class],
            $maxRetries,
            $initialWait,
            $exponent
        );
        return $retryWrapper->executeInOnePass();
    }
}