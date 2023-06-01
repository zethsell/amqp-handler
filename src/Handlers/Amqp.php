<?php

namespace Zeth\AmqpHandler;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Amqp
{
    protected static AMQPStreamConnection $connection;
    protected static AMQPChannel $channel;
    protected static string $queue;

    private function __construct(string $host, int $port, string $username, string $password)
    {
        self::$connection = new AMQPStreamConnection($host, $port, $username, $password);
        self::$channel = self::$connection->channel();
    }

    public static function connect(?string $host, ?int $port, ?string $username, ?string $password)
    {
        return new Amqp($host, $port, $username, $password);
    }

    public function queue(string $queue): Amqp
    {
        self::$queue = $queue;
        self::$channel->queue_declare($queue,  false, true, false, false);
        return $this;
    }

    public function publish(AMQPMessage $payload, string $exchange, string $route)
    {
        self::$channel->basic_publish($payload, $exchange, $route);
    }

    public function consume(callable $callback): void
    {
        self::$channel->basic_consume(self::$queue, '', false, true, false, false, $callback);
    }

    public function isConsuming()
    {
        return self::$channel->is_consuming();
    }

    public function wait()
    {
        return self::$channel->wait();
    }

    public function close(): void
    {
        self::$channel->close();
        self::$connection->close();
    }
}
