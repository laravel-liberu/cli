<?php

namespace LaravelLiberu\Cli\Services\Writers\Table;

use LaravelLiberu\Cli\Services\Writers\Helpers\Controller as BaseController;
use LaravelLiberu\Cli\Services\Writers\Helpers\Controllers as BaseControllers;

class Controllers extends BaseControllers
{
    private const Routes = ['initTable', 'tableData', 'exportExcel'];

    public function create($permission): BaseController
    {
        return new Controller($this->choices, $permission);
    }

    public function routes(): array
    {
        return static::Routes;
    }
}
