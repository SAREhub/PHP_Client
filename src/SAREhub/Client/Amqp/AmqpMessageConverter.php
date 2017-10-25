<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use SAREhub\Client\Amqp\AmqpMessageHeaders as AMH;
use SAREhub\Client\Message\BasicMessage;
use SAREhub\Client\Message\Message;

class AmqpMessageConverter
{

    /**
     * Converts AMQP message to Client Message
     * @param AMQPMessage $message
     * @return Message
     */
    public function convertFrom(AMQPMessage $message)
    {
        return BasicMessage::newInstance()
            ->setBody($message->getBody())
            ->setHeaders([
                AMH::CONSUMER_TAG => $message->get('consumer_tag'),
                AMH::DELIVERY_TAG => $message->get('delivery_tag'),
                AMH::REDELIVERED => $message->get('redelivered'),
                AMH::EXCHANGE => $message->get('exchange'),
                AMH::ROUTING_KEY => $message->get('routing_key'),
                AMH::CONTENT_TYPE => $this->extractProperty('content_type', $message),
                AMH::CONTENT_ENCODING => $this->extractProperty('content_encoding', $message),
                AMH::REPLY_TO => $this->extractProperty('reply_to', $message),
                AMH::CORRELATION_ID => $this->extractProperty('correlation_id', $message),
                AMH::PRIORITY => $this->extractProperty('priority', $message)
            ]);
    }

    private function extractProperty($name, AMQPMessage $message)
    {
        return $message->has($name) ? $message->get($name) : null;
    }

    /**
     * Converts Client Message to AMQP message
     * @param Message $message
     * @return AMQPMessage
     */
    public function convertTo(Message $message)
    {
        $properties = [];
        foreach (AmqpMessageHeaders::getPropertyHeaders() as $header) {
            if ($message->hasHeader($header)) {
                $properties[AmqpMessageHeaders::getMessagePropertyName($header)] = $message->getHeader($header);
            }
        }
        return new AMQPMessage($message->getBody(), $properties);
    }
}