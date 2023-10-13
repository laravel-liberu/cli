<?php

namespace LaravelLiberu\Cli\Services;

use LaravelLiberu\Cli\Contracts\BulkProvider;
use LaravelLiberu\Cli\Contracts\StubProvider;
use LaravelLiberu\Cli\Exceptions\WriterProvider;
use LaravelLiberu\Cli\Services\StubWriters\BulkWriter;
use LaravelLiberu\Cli\Services\StubWriters\Writer;

class WriterFactory
{
    public static function make(object $provider)
    {
        if ($provider instanceof StubProvider) {
            return new Writer($provider);
        }

        if ($provider instanceof BulkProvider) {
            return new BulkWriter($provider);
        }

        throw WriterProvider::unknown($provider);
    }
}
