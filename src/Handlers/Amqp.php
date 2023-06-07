<?php

namespace Zeth\AmqpHandler;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Amqp
{
    protected static AMQPStreamConnection $connection;
    protected static AMQPChannel $channel;
    protected static string $queue;

    private function __construct(string $host, int $port, string $username, string $password, ?bool $ssl = null)
    {
        self::$connection = (is_null($ssl))
            ? new AMQPStreamConnection($host, $port, $username, $password)
            : new AMQPSSLConnection(
                $host,
                $port,
                $username,
                $password,
                '/',
                ['verify_peer' => true],
            );

        self::$channel = self::$connection->channel();
    }

    public static function connect(?string $host, ?int $port, ?string $username, ?string $password, ?bool $ssl)
    {
        return new Amqp($host, $port, $username, $password, $ssl);
    }

    public function queue(string $queue): Amqp
    {
        self::$queue = $queue;
        self::$channel->queue_declare($queue, false, true, false, false);
        return $this;
    }

    public function publish(AMQPMessage $payload, string $route, string $exchange = '')
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
