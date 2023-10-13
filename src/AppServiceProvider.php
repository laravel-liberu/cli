<?php

namespace LaravelLiberu\Cli;

use Illuminate\Support\ServiceProvider;
use LaravelLiberu\Cli\Commands\Cli;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->load()
            ->publish()
            ->commands(Cli::class);
    }

    private function load()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/model.php', 'liberu.structures.model');
        $this->mergeConfigFrom(__DIR__.'/../config/menu.php', 'liberu.structures.menu');
        $this->mergeConfigFrom(__DIR__.'/../config/permissions.php', 'liberu.structures.permissions');
        $this->mergeConfigFrom(__DIR__.'/../config/package.php', 'liberu.structures.package');
        $this->mergeConfigFrom(__DIR__.'/../config/params.php', 'liberu.structures.params');
        $this->mergeConfigFrom(__DIR__.'/../config/files.php', 'liberu.structures.files');
        $this->mergeConfigFrom(
            __DIR__.'/../config/permissionGroup.php', 'liberu.structures.permissionGroup'
        );

        return $this;
    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/../config' => config_path('liberu/structures'),
        ], ['cli-config', 'liberu-config']);

        return $this;
    }
}
