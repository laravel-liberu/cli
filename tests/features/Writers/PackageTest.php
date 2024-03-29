<?php

use Illuminate\Support\Facades\File;
use LaravelLiberu\Cli\Services\Writers\Package;
use LaravelLiberu\Cli\Tests\Cli;
use LaravelLiberu\Helpers\Services\Obj;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use Cli;

    private $root;
    private $choices;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = 'cli_tests_tmp';

        $this->initChoices();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        File::deleteDirectory($this->root);
    }

    /** @test */
    public function can_create_composer()
    {
        $this->write(Package::class);

        $this->assertCliFileContains([
            '"name": "liberu/cli"',
            '"Liberu\\\\Cli\\\\": "src/"',
            '"Liberu\\\\Cli\\\\AppServiceProvider"',
            '"Liberu\\\\Cli\\\\AuthServiceProvider"',
        ], ['composer.json']);
    }

    /** @test */
    public function can_create_readme()
    {
        $this->write(Package::class);

        $this->assertCliFileContains('###  liberu - cli', 'README.md');
    }

    /** @test */
    public function can_create_licence()
    {
        $this->write(Package::class);

        $this->assertCliFileContains('Copyright (c) '.now()->format('Y').' liberu', ['LICENSE']);
    }

    /** @test */
    public function can_create_config()
    {
        $this->choices->get('package')->put('config', true);

        $this->write(Package::class);

        $this->assertFileExists($this->path(['config',  'cli.php']));
    }

    /** @test */
    public function can_create_provider()
    {
        $this->choices->get('package')->put('providers', true);

        $this->write(Package::class);

        $this->assertProvidersContains([
            'namespace Liberu\Cli',
            'class AppServiceProvider extends ServiceProvider',
        ], ['AppServiceProvider.php']);

        $this->assertProvidersContains([
            'namespace Liberu\Cli',
            'class AuthServiceProvider extends ServiceProvider',
        ], ['AuthServiceProvider.php']);
    }

    protected function choices()
    {
        return new Obj([
            'package' => [
                'vendor' => 'liberu',
                'name' => 'cli',
            ],
        ]);
    }

    protected function params()
    {
        return new Obj([
            'root' => $this->root,
            'namespace' => 'Liberu\Cli\App',
            'rootSegment' => 'app',
        ]);
    }
}
