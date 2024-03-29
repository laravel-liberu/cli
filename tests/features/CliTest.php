<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use LaravelLiberu\Cli\Enums\Options;
use LaravelLiberu\Cli\Tests\Cli;
use LaravelLiberu\Helpers\Services\Obj;
use Tests\TestCase;

class CliTest extends TestCase
{
    use Cli;

    private $choice;
    private $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->choice = Options::choices()->first();

        $this->root = '';

        Config::set("liberu.structures{Str::camel({$this->choice})}.attributes", ['name' => null]);
    }

    protected function tearDown(): void
    {
        Cache::forget('cli_data');

        $this->deleteMigration('create_structure_for_test');

        parent::tearDown();
    }

    /** @test */
    public function cannot_config_choice_without_requirements()
    {
        $dependent = $this->dependent();
        $requirements = $this->config($dependent)->implode(', ');

        $this->artisan('liberu:cli')
            ->expectsQuestion('Choose element to configure', $dependent)
            ->expectsOutput('You must configure first: '.$requirements)
            ->expectsQuestion('Choose element to configure', Options::Exit);
    }

    /** @test */
    public function can_reload_session_if_available()
    {
        Cache::put('cli_data', [
            'params' => new Obj(),
            'choices' => new Obj(['files' => []]),
            'configured' => new Collection(),
            'validates' => true,
        ]);

        $this->artisan('liberu:cli')
            ->expectsQuestion('Do you want to restore the last session?', 'yes')
            ->expectsQuestion('Choose element to configure', Options::Exit);
    }

    /** @test */
    public function can_save_after_choice_was_configured()
    {
        $this->artisan('liberu:cli')
            ->expectsQuestion('Choose element to configure', $this->choice)
            ->expectsQuestion('Configure '.$this->choice, true)
            ->expectsQuestion('name', 'test')
            ->expectsQuestion('Choose element to configure', Options::Exit);

        $this->assertEquals('test', Cache::get('cli_data')['choices'][Str::camel($this->choice)]['name']);
    }

    /** @test */
    public function can_remove_saved_session_after_generate()
    {
        $this->artisan('liberu:cli')
            ->expectsQuestion('Choose element to configure', $this->choice)
            ->expectsQuestion('Configure '.$this->choice, true)
            ->expectsQuestion('name', 'test')
            ->expectsQuestion('Choose element to configure', Options::Generate);

        $this->assertFalse(Cache::has('cli_data'));
    }

    /** @test */
    public function cannot_generate_with_nothing_configured()
    {
        $this->artisan('liberu:cli')
            ->expectsQuestion('Choose element to configure', Options::Generate)
            ->expectsOutput('There is nothing configured yet!')
            ->expectsQuestion('Choose element to configure', Options::Exit);
    }

    /** @test */
    public function cannot_generate_with_failed_validation()
    {
        $this->artisan('liberu:cli')
            ->expectsQuestion('Choose element to configure', Options::Model)
            ->expectsQuestion('Configure '.Options::Model, true)
            ->expectsQuestion('name', 'test\\test')
            ->expectsQuestion('Choose element to configure', Options::Generate)
            ->expectsOutput('Your configuration has errors:')
            ->expectsQuestion('Choose element to configure', Options::Exit);
    }

    /** @test */
    public function can_generate_with_disabled_validation()
    {
        $this->artisan('liberu:cli')
            ->expectsQuestion('Choose element to configure', Options::Model)
            ->expectsQuestion('Configure '.Options::Model, true)
            ->expectsQuestion('name', 'test\\test')
            ->expectsQuestion('Choose element to configure', Options::ToggleValidation)
            ->expectsQuestion('Choose element to configure', Options::Generate)
            ->assertExitCode(0);
    }

    private function dependent()
    {
        return Options::choices()
            ->first(fn ($choice) => $this->config($choice)->isNotEmpty());
    }

    private function config($choice)
    {
        return new Collection(
            config('liberu.structures.'.Str::camel($choice).'.requires')
        );
    }
}
