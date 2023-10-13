<?php

namespace LaravelLiberu\Cli\Services\Writers\Form;

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
        $name = Str::camel($this->model->get('name'));

        return $this->path("{$name}.json");
    }

    public function fromTo(): array
    {
        return ['${permissionGroup}' => $this->group];
    }

    public function stub(): string
    {
        return Stub::get('template');
    }

    private function path(?string $filename = null): string
    {
        return Path::get([$this->rootSegment, 'Forms', 'Templates'], $filename);
    }
}
