<?php

namespace Zeth\AmqpHandler\Contracts;

interface IEnvReader
{
    public static function get(string $key): string;

    public static function readEnvFile(): array;
}
