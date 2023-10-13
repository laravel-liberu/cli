<?php

use Illuminate\Support\Facades\File;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Segments;
use LaravelLiberu\Cli\Services\Writers\Views;
use LaravelLiberu\Cli\Tests\Cli;
use LaravelLiberu\Helpers\Services\Obj;
use Tests\TestCase;

class ViewsTest extends TestCase
{
    use Cli;

    private string $root;
    private Choices $choices;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = 'cli_tests_tmp';

        $this->initChoices();
        Segments::ucfirst(false);
        Path::segments();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        File::deleteDirectory($this->root);
    }

    /** @test */
    public function can_create_views()
    {
        $this->write(Views::class);

        $this->choices->get('permissions')->each(fn ($perm) => (
            $this->assertViewPageContains("name: '".ucfirst($perm)."',", $perm)
        ));
    }

    protected function choices()
    {
        return new Obj([
            'permissionGroup' => ['name' => 'group.testModels'],
            'model' => ['name' => 'TestModel'],
            'permissions' => $this->permissions()
                ->only(['index', 'create', 'show', 'edit']),
        ]);
    }
}
