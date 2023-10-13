<?php

namespace LaravelLiberu\Cli\Services\Writers;

use Illuminate\Support\Collection;
use LaravelLiberu\Cli\Contracts\BulkProvider;
use LaravelLiberu\Cli\Contracts\PreparesBulkWriting;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Segments;
use LaravelLiberu\Cli\Services\Writers\Helpers\Stub;
use LaravelLiberu\Cli\Services\Writers\Views\Views as Bulk;

class Views implements BulkProvider, PreparesBulkWriting
{
    public function __construct(private Choices $choices)
    {
    }

    public function prepare(): void
    {
        Segments::ucfirst(false);
        Path::segments();
        Stub::folder('views');
    }

    public function collection(): Collection
    {
        return new Collection([
            new Bulk($this->choices),
        ]);
    }
}
