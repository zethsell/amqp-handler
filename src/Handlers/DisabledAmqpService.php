<?php

namespace Zeth\AmqpHandler;

use PhpAmqpLib\Message\AMQPMessage;

class DisabledAmqpService extends PhpAmqpService
{
    public function connect(): self
    {

        return $this;
    }

    public function queue(string $queue): self
    {
        return $this;
    }

    public function queueDelete(string $queue): self
    {
        return $this;
    }

    public function publish(AMQPMessage $payload, string $exchange = '', string $route='')
    {
        return $this;
    }

    public function consume(callable $callback): void
    {
    }

    public function isConsuming()
    {
        return 0;
    }

    public function wait()
    {
        return null;
    }

    public function close(): void
    {
        
    }
}
