<?php

namespace Zeth\AmqpHandler\DTO;

class ConnectParametersDTO
{
    public readonly bool $consumeNoAck;
    public readonly string $host;
    public readonly int $port;
    public readonly string $username;
    public readonly string $password;
    public readonly bool $ssl;
    public readonly string $connectionName;
    public readonly string $timeout;
    public readonly string $vhost;

    public function __construct(public readonly array $_data)
    {
        $this->host             = $_data['host']  ?? 'localhost';
        $this->port             = $_data['port']  ?? 5672;
        $this->username         = $_data['username']  ?? 'guest';
        $this->password         = $_data['password']  ?? 'guest';
        $this->ssl              = $_data['ssl']  ?? false;
        $this->connectionName   = $_data['connectionName']  ?? 'defaultName';
        $this->timeout          = $_data['timeout']  ?? 0;
        $this->vhost            = $_data['vhost']  ?? '/';
        $this->consumeNoAck     = $_data['consumeNoAck']  ?? false;
        
    }
}
