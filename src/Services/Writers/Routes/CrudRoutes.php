<?php

namespace LaravelLiberu\Cli\Services\Writers\Routes;

use Illuminate\Support\Collection;
use LaravelLiberu\Cli\Contracts\BulkProvider;
use LaravelLiberu\Cli\Services\Choices;

class CrudRoutes implements BulkProvider
{
    private const Routes = ['create', 'edit', 'index', 'show'];

    private Choices $choices;
    private Collection $permissions;

    public function __construct(Choices $choices)
    {
        $this->choices = $choices;
        $this->permissions = $choices->get('permissions')->filter()->keys();
    }

    public function collection(): Collection
    {
        return $this->permissions->intersect(self::Routes)
            ->reduce(fn ($collection, $permission) => $collection
                ->push(new CrudRoute($this->choices, $permission)), new Collection());
    }
}
