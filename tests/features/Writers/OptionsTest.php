<?php

use Illuminate\Support\Facades\File;
use LaravelLiberu\Cli\Services\StubWriters\Writer;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Segments;
use LaravelLiberu\Cli\Services\Writers\Options;
use LaravelLiberu\Cli\Tests\Cli;
use Tests\TestCase;

class OptionsTest extends TestCase
{
    use Cli;

    private $root;
    private $choices;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = 'cli_tests_tmp';

        $this->initChoices();
        Segments::ucfirst();
        Path::segments();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        File::deleteDirectory($this->root);
    }

    /** @test */
    public function can_create_controller()
    {
        (new Writer(new Options($this->choices)))->handle();

        $this->assertControllerContains([
            'namespace Namespace\App\Http\Controllers\Group\TestModels;',
            'class Options extends Controller',
            'protected string $model = TestModel::class;',
        ], 'Options');
    }
}
