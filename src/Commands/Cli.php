<?php

namespace LaravelLiberu\Cli\Commands;

use Illuminate\Console\Command;
use LaravelLiberu\Cli\Enums\Options;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Config;
use LaravelLiberu\Cli\Services\Generator;
use LaravelLiberu\Cli\Services\Status;

class Cli extends Command
{
    protected $signature = 'liberu:cli';
    protected $description = 'Create a new Laravel Liberu Structure';

    private Choices $choices;

    public function __construct()
    {
        parent::__construct();

        $this->choices = new Choices($this);
    }

    public function handle()
    {
        $this->info('Create a new Laravel Liberu Structure');
        $this->newLine();

        $this->choices->restore();

        $this->index();
    }

    private function index()
    {
        $choice = $this->input();

        switch ($choice) {
            case Options::Exit:
                break;
            case Options::Generate:
                if (! (new Generator($this->choices))->handle()) {
                    $this->index();
                }
                break;
            default:
                (new Config($this->choices))->fill($choice);
                $this->index();
                break;
        }
    }

    private function input()
    {
        return (new Status($this->choices))
            ->display()
            ->choice();
    }
}
