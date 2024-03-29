<?php

namespace LaravelLiberu\Cli\Services\Writers\Form;

use Illuminate\Support\Str;
use LaravelLiberu\Cli\Contracts\StubProvider;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Directory;
use LaravelLiberu\Cli\Services\Writers\Helpers\Namespacer;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Stub;
use LaravelLiberu\Helpers\Services\Obj;

class Validator implements StubProvider
{
    private Obj $model;
    private string $rootSegment;

    public function __construct(Choices $choices)
    {
        $this->model = $choices->get('model');
        $this->rootSegment = $choices->params()->get('rootSegment');
    }

    public function prepare(): void
    {
        Directory::prepare($this->path());
    }

    public function filePath(): string
    {
        return $this->path("Validate{$this->model->get('name')}.php");
    }

    public function fromTo(): array
    {
        return [
            '${namespace}' => Namespacer::get(['Http', 'Requests']),
            '${Model}' => Str::ucfirst($this->model->get('name')),
        ];
    }

    public function stub(): string
    {
        return Stub::get('request');
    }

    private function path(?string $filename = null): string
    {
        return Path::get([$this->rootSegment, 'Http', 'Requests'], $filename);
    }
}
