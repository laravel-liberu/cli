<?php

namespace LaravelLiberu\Cli\Services\Writers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Directory;
use LaravelLiberu\Helpers\Services\Obj;

class Migration
{
    private Obj $model;
    private ?string $root;

    public function __construct(Choices $choices)
    {
        $this->model = $choices->get('model');
        $this->root = $choices->params()->get('root');
    }

    public function handle()
    {
        $path = $this->path();

        Directory::prepare($path);

        $name = Str::plural(Str::snake($this->model->get('name')));

        Artisan::call('make:migration', [
            'name' => "create_{$name}_table",
            '--path' => $path,
        ]);
    }

    private function path()
    {
        return Collection::wrap([$this->root, 'database', 'migrations'])
            ->filter()->implode(DIRECTORY_SEPARATOR);
    }
}
