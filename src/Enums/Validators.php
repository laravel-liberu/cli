<?php

namespace LaravelLiberu\Cli\Enums;

use LaravelLiberu\Cli\Services\Validators\Menu;
use LaravelLiberu\Cli\Services\Validators\Model;
use LaravelLiberu\Enums\Services\Enum;

class Validators extends Enum
{
    public static array $data = [
        Options::Model => Model::class,
        Options::Menu => Menu::class,
    ];
}
