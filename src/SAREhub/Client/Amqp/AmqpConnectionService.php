<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use SAREhub\Commons\Service\ServiceSupport;

class AmqpConnectionService extends ServiceSupport
{
    /**
     * @var AmqpConnectionProvider
     */
    private $connectionProvider;

    /**
     * @var AbstractConnection
     */
    private $connection;

    /**
     * @var AmqpChannelWrapper[]
     */
    private $channels = [];

    public function __construct(AmqpConnectionProvider $connectionProvider)
    {
        $this->connectionProvider = $connectionProvider;
    }

    public function addChannel(AmqpChannelWrapper $channel): void
    {
        $this->channels[] = $channel;
        $this->getLogger()->info("Added channel");
    }

    protected function doStart()
    {
        $this->connection = $this->connectionProvider->get();
        foreach ($this->channels as $channel) {
            $channel->setWrappedChannel($this->connection->channel());
            $channel->start();
        }
    }

    protected function doTick()
    {
        try {
            foreach ($this->channels as $channel) {
                $channel->tick();
            }
        } catch (AMQPRuntimeException | AMQPIOException $e) {
            $this->getLogger()->warning("Reconnecting...", ["reason" => $e->getMessage()]);
            $this->reconnect();
            $this->getLogger()->warning("Reconnected", ["reason" => $e->getMessage()]);
        }
    }

    private function reconnect(): void
    {
        $this->connection->reconnect();
        foreach ($this->channels as $channel) {
            $channel->setWrappedChannel($this->connection->channel());
            $channel->updateState();
        }
    }

    protected function doStop()
    {
        foreach ($this->channels as $channel) {
            $channel->stop();
        }
        $this->connection->close();
    }
}
