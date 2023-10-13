<?php

namespace LaravelLiberu\Cli\Services\StubWriters;

use LaravelLiberu\Cli\Contracts\BulkProvider;
use LaravelLiberu\Cli\Contracts\PreparesBulkWriting;
use LaravelLiberu\Cli\Contracts\Writer as Contract;
use LaravelLiberu\Cli\Services\WriterFactory;

class BulkWriter implements Contract
{
    public function __construct(private BulkProvider $bulkProvider)
    {
    }

    public function handle(): void
    {
        if ($this->bulkProvider instanceof PreparesBulkWriting) {
            $this->bulkProvider->prepare();
        }

        $this->bulkProvider->collection()
            ->each(fn ($provider) => WriterFactory::make($provider)->handle());
    }
}
