<?php

namespace LaravelLiberu\Cli\Services\Writers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelLiberu\Cli\Contracts\StubProvider;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Directory;
use LaravelLiberu\Cli\Services\Writers\Helpers\EnsoStructure\Mapping;
use LaravelLiberu\Cli\Services\Writers\Helpers\EnsoStructure\Permissions;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Stub;
use LaravelLiberu\Helpers\Services\Obj;

class EnsoStructure implements StubProvider
{
    private ?Obj $model;
    private ?Obj $menu;
    private ?Collection $permissions;
    private string $group;

    public function __construct(Choices $choices)
    {
        $this->model = $choices->get('model');
        $this->menu = $choices->get('menu');
        $this->group = $choices->get('permissionGroup')->get('name');
        $this->permissions = $choices->has('permissions')
            ? $choices->get('permissions')->filter()->keys()
            : null;
    }

    public function prepare(): void
    {
        Path::segments(false);
        Stub::folder('structure');
        Directory::prepare($this->path());
    }

    public function filePath(): string
    {
        $timestamp = Carbon::now()->format('Y_m_d_His');
        $structure = Str::snake(Str::plural($this->entity()));

        return $this->path("{$timestamp}_create_structure_for_{$structure}.php");
    }

    public function fromTo(): array
    {
        $mapping = (new Mapping($this->menu, $this->group));

        return [
            '${Entity}' => Str::plural($this->entity()),
            '${menu}' => $mapping->menu(),
            '${parentMenu}' => $mapping->parentMenu(),
            '${permissions}' => (new Permissions(
                $this->model, $this->permissions, $this->group
            ))->get(),
        ];
    }

    public function stub(): string
    {
        return Stub::get('migration');
    }

    private function entity(): string
    {
        return $this->model
            ? Str::ucfirst($this->model->get('name'))
            : Str::ucfirst($this->menu->get('name'));
    }

    private function path(?string $filename = null): string
    {
        return Path::get(['database', 'migrations'], $filename);
    }
}
