<?php

namespace LaravelLiberu\Cli\Services\Writers\Package;

use Illuminate\Support\Collection;
use LaravelLiberu\Cli\Contracts\StubProvider;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Directory;
use LaravelLiberu\Cli\Services\Writers\Helpers\Stub;

class Config implements StubProvider
{
    private string $root;
    private $package;

    public function __construct(Choices $choices)
    {
        $this->package = $choices->get('package');
        $this->root = $choices->params()->get('root');
    }

    public function prepare(): void
    {
        Directory::prepare($this->path());
    }

    public function filePath(): string
    {
        return $this->path("{$this->package->get('name')}.php");
    }

    public function fromTo(): array
    {
        return [];
    }

    public function stub(): string
    {
        return Stub::get('config');
    }

    private function path(?string $filename = null): string
    {
        return Collection::wrap([
            $this->root, 'config', $filename,
        ])->filter()->implode(DIRECTORY_SEPARATOR);
    }
}
