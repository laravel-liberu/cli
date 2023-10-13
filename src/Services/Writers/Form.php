<?php

namespace LaravelLiberu\Cli\Services\Writers;

use Illuminate\Support\Collection;
use LaravelLiberu\Cli\Contracts\BulkProvider;
use LaravelLiberu\Cli\Contracts\PreparesBulkWriting;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Form\Builder;
use LaravelLiberu\Cli\Services\Writers\Form\Controllers;
use LaravelLiberu\Cli\Services\Writers\Form\Template;
use LaravelLiberu\Cli\Services\Writers\Form\Validator;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Segments;
use LaravelLiberu\Cli\Services\Writers\Helpers\Stub;

class Form implements BulkProvider, PreparesBulkWriting
{
    public function __construct(private Choices $choices)
    {
    }

    public function prepare(): void
    {
        Segments::ucfirst();
        Path::segments();
        Stub::folder('form');
    }

    public function collection(): Collection
    {
        return new Collection([
            new Template($this->choices),
            new Builder($this->choices),
            new Controllers($this->choices),
            new Validator($this->choices),
        ]);
    }
}
