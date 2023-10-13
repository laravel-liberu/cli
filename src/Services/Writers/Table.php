<?php

namespace LaravelLiberu\Cli\Services\Writers;

use Illuminate\Support\Collection;
use LaravelLiberu\Cli\Contracts\BulkProvider;
use LaravelLiberu\Cli\Contracts\PreparesBulkWriting;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Segments;
use LaravelLiberu\Cli\Services\Writers\Helpers\Stub;
use LaravelLiberu\Cli\Services\Writers\Table\Builder;
use LaravelLiberu\Cli\Services\Writers\Table\Controllers;
use LaravelLiberu\Cli\Services\Writers\Table\Template;

class Table implements BulkProvider, PreparesBulkWriting
{
    public function __construct(private Choices $choices)
    {
    }

    public function prepare(): void
    {
        Segments::ucfirst();
        Path::segments();
        Stub::folder('table');
    }

    public function collection(): Collection
    {
        return new Collection([
            new Template($this->choices),
            new Builder($this->choices),
            new Controllers($this->choices),
        ]);
    }
}
