<?php

use Illuminate\Support\Facades\File;
use LaravelLiberu\Cli\Services\Choices;
use LaravelLiberu\Cli\Services\Writers\Form;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Segments;
use LaravelLiberu\Cli\Tests\Cli;
use Tests\TestCase;

class FormTest extends TestCase
{
    use Cli;

    private $root;
    private Choices $choices;

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
    public function can_create_builder()
    {
        $this->write(Form::class);

        $this->assertFormBuilderContains([
            'class TestModel',
            'public function edit(Model $testModel)',
            'return $this->form->edit($testModel);',
        ]);
    }

    /** @test */
    public function can_create_controller()
    {
        $this->setPermission('edit');

        $this->write(Form::class);

        $this->assertControllerContains([
            'class Edit extends Controller',
            'public function __invoke(TestModel $testModel, Form $form)',
        ], 'Edit');
    }

    /** @test */
    public function can_create_index()
    {
        $this->setPermission('index');

        $this->write(Form::class);

        $this->assertControllerContains([
            'namespace Namespace\App\Http\Controllers\Group\TestModels;',
            'class Index extends Controller',
            'public function __invoke(Request $request)',
        ], 'Index');
    }

    /** @test */
    public function can_create_show()
    {
        $this->setPermission('show');

        $this->write(Form::class);

        $this->assertControllerContains([
            'namespace Namespace\App\Http\Controllers\Group\TestModels;',
            'use App\TestModel;',
            'class Show extends Controller',
            'public function __invoke(TestModel $testModel)',
            'return [\'testModel\' => $testModel]',
        ], 'Show');
    }

    /** @test */
    public function can_create_create()
    {
        $this->setPermission('create');

        $this->write(Form::class);

        $this->assertControllerContains([
            'namespace Namespace\App\Http\Controllers\Group\TestModels;',
            'class Create extends Controller',
            'public function __invoke(TestModel $form)',
            'return [\'form\' => $form->create()]',
        ], 'Create');
    }

    /** @test */
    public function can_create_destroy()
    {
        $this->setPermission('destroy');

        $this->write(Form::class);

        $this->assertControllerContains([
            'namespace Namespace\App\Http\Controllers\Group\TestModels;',
            'use App\TestModel;',
            'class Destroy extends Controller',
            'public function __invoke(TestModel $testModel)',
            "'message' => __('The test model was successfully deleted')",
        ], 'Destroy');
    }

    /** @test */
    public function can_create_update()
    {
        $this->setPermission('update');

        $this->write(Form::class);

        $this->assertControllerContains([
            'namespace Namespace\App\Http\Controllers\Group\TestModels;',
            'use App\TestModel;',
            'use Namespace\App\Http\Requests\Group\ValidateTestModel',
            'class Update extends Controller',
            'public function __invoke(ValidateTestModel $request, TestModel $testModel)',
            "return ['message' => __('The test model was successfully updated')]",
        ], 'Update');
    }

    /** @test */
    public function can_create_store()
    {
        $this->setPermission('store');

        $this->write(Form::class);

        $this->assertControllerContains([
            'namespace Namespace\App\Http\Controllers\Group\TestModels;',
            'use App\TestModel;',
            'use Namespace\App\Http\Requests\Group\ValidateTestModel',
            'class Store extends Controller',
            '$testModel->fill($request->validated())->save()',
            'public function __invoke(ValidateTestModel $request, TestModel $testModel)',
        ], 'Store');
    }

    /** @test */
    public function can_create_request()
    {
        $this->setPermission('store');

        $this->write(Form::class);

        $this->assertValidatorContains([
            'namespace Namespace\\App\\Http\\Requests\\Group;',
            'class ValidateTestModel extends FormRequest',
        ], 'ValidateTestModel');
    }
}
