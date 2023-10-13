<?php

namespace LaravelLiberu\Cli\Services\Writers\Views;

use Illuminate\Support\Collection;
use LaravelLiberu\Cli\Contracts\BulkProvider;
use LaravelLiberu\Cli\Services\Choices;

class Views implements BulkProvider
{
    private const Views = ['create', 'edit', 'index', 'show'];

    private Choices $choices;
    private Collection $permissions;

    public function __construct(Choices $choices)
    {
        $this->choices = $choices;
        $this->permissions = $choices->get('permissions')->filter()->keys();
    }

    public function collection(): Collection
    {
        return $this->permissions->intersect(self::Views)
            ->reduce(fn ($collection, $permission) => $collection
                ->push(new View($this->choices, $permission)), new Collection());
    }
}
