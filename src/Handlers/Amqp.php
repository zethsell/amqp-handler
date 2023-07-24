<?php

namespace Zeth\AmqpHandler;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnectionConfig;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Amqp
{
    protected static AMQPStreamConnection $connection;
    protected static AMQPChannel $channel;
    protected static string $queue;
    protected static array $data;

    public static function connect(?string $host, ?int $port, ?string $username, ?string $password, ?bool $ssl, ?string $connectionName = '')
    {

        $connectionConfig = new AMQPConnectionConfig();
        $connectionConfig->setConnectionName($connectionName);

        self::$data = compact('host',  'port',  'username',  'password', 'ssl', 'connectionName');
        self::$connection = (!$ssl)
            ? new AMQPStreamConnection($host, $port, $username, $password)
            : new AMQPSSLConnection(
                $host,
                $port,
                $username,
                $password,
                '/',
                ['verify_peer' => true],
                ['read_write_timeout' => 360, 'heartbeat' => 40],
                'ssl',
                $connectionConfig
            );

        self::$channel = self::$connection->channel();

        return new Amqp();
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

    public function renewConnection()
    {
        self::close();
        self::connect(
            self::$data['host'],
            self::$data['port'],
            self::$data['username'],
            self::$data['password'],
            self::$data['ssl'],
            self::$data['connectionName']
        );
    }
}
