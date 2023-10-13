<?php

namespace LaravelLiberu\Cli\Services\Writers\Table;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelLiberu\Cli\Contracts\StubProvider;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Directory;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Stub;
use LaravelLiberu\Helpers\Services\Obj;

class Template implements StubProvider
{
    private Obj $model;
    private string $group;
    private string $rootSegment;

    public function __construct(Choices $choices)
    {
        $this->model = $choices->get('model');
        $this->group = $choices->get('permissionGroup')->get('name');
        $this->rootSegment = $choices->params()->get('rootSegment');
    }

    public function prepare(): void
    {
        Directory::prepare($this->path());
    }

    public function filePath(): string
    {
        $name = Str::camel(Str::plural($this->model->get('name')));

        return $this->path("{$name}.json");
    }

    public function fromTo(): array
    {
        $model = $this->model->get('name');
        $name = Collection::wrap(explode('_', Str::snake($model)))->implode(' ');

        return [
            '${permissionGroup}' => $this->group,
            '${name}' => Str::title($name),
            '${table}' => Str::snake(Str::plural($model)),
        ];
    }

    public function stub(): string
    {
        return Stub::get('template');
    }

    private function path(?string $filename = null): string
    {
        return Path::get([$this->rootSegment, 'Tables', 'Templates'], $filename);
    }
}
