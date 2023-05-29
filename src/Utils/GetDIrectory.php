<?php

namespace Zeth\AmqpHandler\Utils;

const CLI_FOLDER = 'vendor/amqp_handler';

function getRootDirectory(): string
{
    return str_contains(__DIR__, CLI_FOLDER)
        ? current(explode(CLI_FOLDER, __DIR__))
        : current(explode('src', __DIR__));
}

function getDirectory(): string
{
    return str_contains(__DIR__, CLI_FOLDER)
        ? current(explode(CLI_FOLDER, __DIR__))
        : current(explode('src', __DIR__));
}

