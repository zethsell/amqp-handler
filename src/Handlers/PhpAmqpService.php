<?php

namespace Zeth\AmqpHandler;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnectionConfig;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Zeth\AmqpHandler\DTO\ConnectParametersDTO;

class PhpAmqpService
{
    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;
    protected string $queue;
    protected array $data;

    private $waitAllowedMethods = null;
    private bool $waitNonBlocking = false;
    private $waitTimeout = 0;

    private string $host = 'localhost';
    private int $port = 5672;
    private string $username = 'guest';
    private string $password = 'guest';
    private bool $ssl = false;
    private string $connectionName = 'defaultName';
    private string $vhost = '/';
    private bool $insist = false;
    private string $login_method = 'AMQPLAIN';
    private string|null $login_response = null;
    private string $locale = 'en_US';
    private float $connection_timeout = 3.0;
    private float $read_write_timeout = 3.0;
    private array|null $context = null;
    private bool $keepalive = false;
    private int $heartbeat = 0;
    private float $channel_rpc_timeout = 0.0;
    private string|null $ssl_protocol = null;
    private AMQPConnectionConfig|null $config = null;

    private bool $consumeNoAck = false;
    private bool $queueDurable = false;
    private bool $queueAutoDelete = false;
    private bool $exchangeAutoDelete = false;
    private bool $exchangeDurable = true;
    private string $exchangeType = 'direct';


    public function connect(): self
    {

        $this->config = new AMQPConnectionConfig();
        $this->config->setConnectionName($this->connectionName);
         
        $this->connection = (!$this->ssl)
            ? new AMQPStreamConnection($this->host, $this->port, $this->username, $this->password, $this->vhost, $this->insist, $this->login_method,
                $this->login_response, $this->locale, $this->connection_timeout, $this->read_write_timeout, $this->context, $this->keepalive,
                $this->heartbeat, $this->channel_rpc_timeout, $this->ssl_protocol, $this->config)
            : new AMQPSSLConnection(
                $this->host,
                $this->port,
                $this->username,
                $this->password,
                $this->vhost,
                ['verify_peer' => false],
                ['read_write_timeout' => 360, 'heartbeat' => 40],
                $this->config
            );

        $this->channel = $this->connection->channel();

        return $this;
    }

    public function setConnectionParameters(ConnectParametersDTO $connectParametersDTO): self{
        $this->host         = $connectParametersDTO->host;
        $this->port         = $connectParametersDTO->port;
        $this->username     = $connectParametersDTO->username;
        $this->password     = $connectParametersDTO->password;
        $this->ssl          = $connectParametersDTO->ssl;
        $this->connectionName = $connectParametersDTO->connectionName;
        $this->waitTimeout  = $connectParametersDTO->timeout;
        $this->vhost        = $connectParametersDTO->vhost;
        $this->consumeNoAck = $connectParametersDTO->consumeNoAck;
        return $this;
    }

    public function exchangeDeclare($exchangeName){
        $this->channel->exchange_declare(
            $exchangeName, 
            $this->exchangeType, # type
            false,    # passive
            $this->exchangeDurable,    # durable
            $this->exchangeAutoDelete     # auto_delete
        );
        return $this;
    }

    public function queue(string $queue): self
    {
        $this->queue = $queue;
        $this->channel->queue_declare($queue, false, $this->queueDurable, false, $this->queueAutoDelete);
//        $this->channel->queue_declare($queue, false, $this->queueDurable, false, $this->queueAutoDelete);
        return $this;
    }

    public function queueDelete(string $queue): self
    {
        $this->channel->queue_delete($queue);
        return $this;
    }

    public function publish(AMQPMessage $payload, string $exchange = '', string $route='')
    {
        $this->channel->basic_publish($payload, $exchange, $route);
    }

    public function consume(callable $callback): void
    {
        $this->channel->basic_consume($this->queue, '', false, $this->consumeNoAck, false,  false, $callback);
    }

    public function isConsuming()
    {
        return $this->channel->is_consuming();
    }

    public function wait()
    {
        return $this->channel->wait($this->waitAllowedMethods, $this->waitNonBlocking, $this->waitTimeout);
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function renewConnection()
    {
        $this->close();
        $this->connect();
    }

    public function getChannel(){
        return $this->channel;
    }

    public function setConsumeNoAck(bool $consumeNoAck): self
    {
        $this->consumeNoAck = $consumeNoAck;
        return $this;
    }

    public function setQueueAutoDelete(bool $queueAutoDelete){
        $this->queueAutoDelete = $queueAutoDelete;
        return $this;
    }
    public function setQueueDurable(bool $queueDurable){
        $this->queueDurable = $queueDurable;
        return $this;
    }

    public function getWaitTimeout(){
        return $this->waitTimeout;
    }

    public function setWaitTimeout($waitTimeout = 0){
        $this->waitTimeout = $waitTimeout;
        return $this;
    }
}
