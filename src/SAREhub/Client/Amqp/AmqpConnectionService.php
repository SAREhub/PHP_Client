<?php


namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Exception\AMQPProtocolConnectionException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use SAREhub\Client\Amqp\Schema\AmqpEnvironmentSchemaCreator;
use SAREhub\Commons\Service\ServiceSupport;

class AmqpConnectionService extends ServiceSupport
{
    /**
     * @var AbstractConnection
     */
    private $connection;

    /**
     * @var AmqpChannelWrapper[]
     */
    private $channels = [];

    public function __construct(AbstractConnection $connection)
    {
        $this->connection = $connection;
    }

    public function createChannel(string $id, AmqpEnvironmentSchemaCreator $schemaCreator): AmqpChannelWrapper
    {
        $channel = new AmqpChannelWrapper($schemaCreator);
        $this->channels[$id] = $channel;
        return $channel;
    }

    protected function doStart()
    {
        if (!$this->connection->isConnected()) {
            $this->reconnect();
            return;
        }

        foreach ($this->channels as $channel) {
            $channel->setWrappedChannel($this->connection->channel());
            $channel->start();
        }
    }

    public function reconnect(): void
    {
        $this->connection->reconnect();
        foreach ($this->channels as $channel) {
            $channel->setWrappedChannel($this->connection->channel());
            $channel->updateState();
        }
    }

    protected function doTick()
    {
        try {
            foreach ($this->channels as $channel) {
                $channel->tick();
            }
        } catch (AMQPProtocolConnectionException $e) {
            $this->reconnect();
            $this->getLogger()->warning("reconnected", [
                "reason" => $e
            ]);
        }
    }

    protected function doStop()
    {
        $this->close();
        foreach ($this->channels as $channel) {
            $channel->stop();
        }
    }

    public function close(): void
    {
        $this->connection->close();
    }
}