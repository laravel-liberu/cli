<?php

use Illuminate\Support\Facades\File;
use LaravelLiberu\Cli\Services\StubWriters\Writer;
use LaravelLiberu\Cli\Services\Writers\Model;
use LaravelLiberu\Cli\Tests\Cli;
use Tests\TestCase;

class ModelTest extends TestCase
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
    public function can_create_model_with_path()
    {
        $this->choices->get('model')->put('path', 'app');

        (new Writer(new Model($this->choices)))->handle();

        $this->assertFileExists("{$this->root}/app/TestModel.php");
    }

    /** @test */
    public function can_create_model()
    {
        (new Writer(new Model($this->choices)))->handle();

        $this->assertCliFileContains('class TestModel extends Model', ['TestModel.php']);
    }
}
