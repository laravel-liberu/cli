<?php

namespace LaravelLiberu\Cli\Contracts;

use Illuminate\Support\Collection;

interface BulkProvider
{
    public function collection(): Collection;
}
