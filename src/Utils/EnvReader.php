<?php

namespace Zeth\AmqpHandler\Utils;

use Zeth\AmqpHandler\Contracts\IEnvReader;

class EnvReader implements IEnvReader
{
    public static function get(string $key): string
    {
        $env = self::readEnvFile();
        return $env[$key];
    }

    public static function readEnvFile(): array
    {
        $directory = getRootDirectory();
        $envFile = $directory . DIRECTORY_SEPARATOR . '.env';
        if (!file_exists($envFile)) {
            error_log('.env file not found');
            throw new \Error('.env file not found');
        }
        return parse_ini_file($envFile);
    }
}
