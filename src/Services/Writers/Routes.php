<?php

namespace LaravelLiberu\Cli\Services\Writers;

use Illuminate\Support\Collection;
use LaravelLiberu\Cli\Contracts\BulkProvider;
use LaravelLiberu\Cli\Contracts\PreparesBulkWriting;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Segments;
use LaravelLiberu\Cli\Services\Writers\Helpers\Stub;
use LaravelLiberu\Cli\Services\Writers\Routes\CrudRoutes;
use LaravelLiberu\Cli\Services\Writers\Routes\SegmentRoutes;

class Routes implements BulkProvider, PreparesBulkWriting
{
    public function __construct(private Choices $choices)
    {
    }

    public function prepare(): void
    {
        Segments::ucfirst(false);
        Stub::folder('routes');
    }

    public function collection(): Collection
    {
        return new Collection([
            new CrudRoutes($this->choices),
            new SegmentRoutes($this->choices),
        ]);
    }
}
