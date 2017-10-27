<?php

namespace SAREhub\Client\Amqp;


use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SAREhub\Client\Amqp\AmqpMessageHeaders as AMH;
use SAREhub\Client\Message\Exchange;

class BasicAmqpMessageProcessConfirmStrategy implements AmqpMessageProcessConfirmStrategy, LoggerAwareInterface
{
    /**
     * @var AmqpChannelWrapper
     */
    private $channel;

    /**
     * @var bool
     */
    private $requeueFailed = true;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(AmqpChannelWrapper $channel)
    {
        $this->channel = $channel;
        $this->logger = new NullLogger();
    }

    public function confirm(Exchange $orginal, Exchange $afterProcess)
    {
        $context = ["orginal" => $orginal, "afterProcess" => $afterProcess];
        $deliveryTag = $orginal->getIn()->getHeader(AMH::DELIVERY_TAG);
        if ($afterProcess->isFailed()) {
            $this->logger->debug('process message failed', $context);
            $this->channel->nack($deliveryTag, false, $this->isRequeueFailed());
        } else {
            $this->logger->debug('process message success', $context);
            $this->channel->ack($deliveryTag, false);
        }
    }

    public function isRequeueFailed(): bool
    {
        return $this->requeueFailed;
    }

    public function setRequeueFailed(bool $requeue): BasicAmqpMessageProcessConfirmStrategy
    {
        $this->requeueFailed = $requeue;
        return $this;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}