<?php

namespace LaravelLiberu\Cli\Services\Writers\Package;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use LaravelLiberu\Cli\Contracts\StubProvider;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Directory;
use LaravelLiberu\Cli\Services\Writers\Helpers\Stub;
use LaravelLiberu\Helpers\Services\Obj;

class Resource implements StubProvider
{
    private string $root;
    private string $namespace;
    private string $resource;
    private Obj $package;

    public function __construct(Choices $choices, $resource)
    {
        $this->package = $choices->get('package');
        $this->root = $choices->params()->get('root');
        $this->namespace = $choices->params()->get('namespace');
        $this->resource = $resource;
    }

    public function prepare(): void
    {
        Directory::prepare($this->path());
    }

    public function filePath(): string
    {
        return $this->path($this->resource);
    }

    public function fromTo(): array
    {
        $segments = Collection::wrap(explode('\\', $this->namespace))->slice(0, 2);

        return [
            '${year}' => Carbon::now()->format('Y'),
            '${vendor}' => $this->package->get('vendor'),
            '${package}' => $this->package->get('name'),
            '${namespace}' => $segments->implode('\\'),
            '${Vendor}' => $segments->first(),
            '${Package}' => $segments->last(),
        ];
    }

    public function stub(): string
    {
        return Stub::get($this->resource);
    }

    private function path(?string $filename = null): string
    {
        return Collection::wrap([$this->root, $filename])
            ->filter()->implode(DIRECTORY_SEPARATOR);
    }
}
