<?php

namespace Zeth\AmqpHandler;

use PhpAmqpLib\Message\AMQPMessage;

class AmqpPayload
{
    public static function compose(string $payload)
    {
        return new AMQPMessage($payload);
    }
}
