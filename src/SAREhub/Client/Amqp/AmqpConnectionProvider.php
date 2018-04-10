<?php


namespace SAREhub\Client\Amqp;


use SAREhub\Commons\Misc\InvokableProvider;

class AmqpConnectionProvider extends InvokableProvider
{
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
        return $this->connectionFactory->create($this->options);
    }
}