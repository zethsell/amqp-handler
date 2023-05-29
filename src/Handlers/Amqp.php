<?php

namespace Zeth\AmqpHandler;

use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Amqp
{
    protected static AMQPStreamConnection $connection;
    protected static AbstractChannel $channel;

    private function __contruct(string $host, int $port, string $username, string $password, string $channel)
    {
        self::$connection = new AMQPStreamConnection($host, $port, $username, $password);
        self::$connection->channel($channel);
    }

    public static function connect(?string $host, ?int $port, ?string $username, ?string $password, ?string $channel)
    {
        return new Amqp($host, $port, $username, $password, $channel);
    }

    public function queue(string|int $channel): Amqp
    {
        self::$channel->queue_declare($channel, false, true, false, false);
        return $this;
    }

    public function publish(AMQPMessage $payload, string $exchange, string $route)
    {
        self::$channel->basic_publish($payload, $exchange, $route);
    }

    public function consume(string $queue, callable $callback): void
    {
        self::$channel->basic_consume($queue, false, true, false, false, $callback);
    }

    public function isConsuming()
    {
        return self::$channel->is_consuming();
    }

    public function close(): void
    {
        self::$channel->close();
        self::$connection->close();
    }
}
