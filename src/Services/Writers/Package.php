<?php

namespace LaravelLiberu\Cli\Services\Writers;

use Illuminate\Support\Collection;
use LaravelLiberu\Cli\Contracts\BulkProvider;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Stub;
use LaravelLiberu\Cli\Services\Writers\Package\Config;
use LaravelLiberu\Cli\Services\Writers\Package\Providers;
use LaravelLiberu\Cli\Services\Writers\Package\Resources;

class Package implements BulkProvider
{
    public function __construct(private Choices $choices)
    {
        Path::segments(false);
        Stub::folder('package');
    }

    public function collection(): Collection
    {
        $files = new Collection([new Resources($this->choices)]);

        if ($this->choices->get('package')->get('config')) {
            $files->push(new Config($this->choices));
        }

        if ($this->choices->get('package')->get('providers')) {
            $files->push(new Providers($this->choices));
        }

        return $files;
    }
}
