<?php

namespace LaravelLiberu\Cli\Services\StubWriters;

use Illuminate\Support\Facades\File;
use LaravelLiberu\Cli\Contracts\StubProvider;
use LaravelLiberu\Cli\Contracts\Writer as Contract;

class Writer implements Contract
{
    public function __construct(private StubProvider $provider)
    {
    }

    public function handle(): void
    {
        $this->provider->prepare();

        File::put($this->provider->filePath(), $this->content());
    }

    private function content(): string
    {
        $fromTo = $this->provider->fromTo();
        [$from, $to] = [array_keys($fromTo), array_values($fromTo)];

        return str_replace($from, $to, $this->provider->stub());
    }
}
