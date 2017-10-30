<?php

namespace SAREhub\Client\Amqp;


use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SAREhub\Client\Amqp\AmqpMessageHeaders as AMH;
use SAREhub\Client\Message\Exchange;
use SAREhub\Client\Message\Message;

class BasicAmqpProcessConfirmStrategy implements AmqpProcessConfirmStrategy, LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $requeueOnReject = true;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public function confirm(AmqpChannelWrapper $channel, Message $orginalIn, Exchange $exchange)
    {
        $consumer = $channel->getConsumer($orginalIn->getHeader(AMH::CONSUMER_TAG));
        if ($consumer->getOptions()->isAutoAckMode()) {
            return;
        }

        $context = ["orginalIn" => $orginalIn, "exchange" => $exchange];
        if ($exchange->isFailed()) {
            $this->logger->debug('processed message failed', $context);
            $channel->reject($orginalIn, $this->isRequeueOnReject());
        } else {
            $this->logger->debug('processed message success', $context);
            $channel->ack($orginalIn);
        }
    }

    public function isRequeueOnReject(): bool
    {
        return $this->requeueOnReject;
    }

    public function setRejectRequeue(bool $requeue): BasicAmqpProcessConfirmStrategy
    {
        $this->requeueOnReject = $requeue;
        return $this;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}