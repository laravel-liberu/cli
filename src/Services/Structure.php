<?php

namespace LaravelLiberu\Cli\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelLiberu\Cli\Services\Writers\LiberuStructure;
use LaravelLiberu\Cli\Services\Writers\Form;
use LaravelLiberu\Cli\Services\Writers\Helpers\Namespacer;
use LaravelLiberu\Cli\Services\Writers\Helpers\Path;
use LaravelLiberu\Cli\Services\Writers\Helpers\Segments;
use LaravelLiberu\Cli\Services\Writers\Migration;
use LaravelLiberu\Cli\Services\Writers\Model;
use LaravelLiberu\Cli\Services\Writers\Options;
use LaravelLiberu\Cli\Services\Writers\Package;
use LaravelLiberu\Cli\Services\Writers\Routes;
use LaravelLiberu\Cli\Services\Writers\Table;
use LaravelLiberu\Cli\Services\Writers\Views;
use LaravelLiberu\Helpers\Services\Obj;

class Structure
{
    private bool $isPackage;

    private array $providers = [
        'table' => Table::class,
        'form' => Form::class,
        'views' => Views::class,
        'routes' => Routes::class,
        'structure' => LiberuStructure::class,
        'options' => Options::class,
        'model' => Model::class,
        'package' => Package::class,
    ];

    public function __construct(private Choices $choices)
    {
    }

    public function handle()
    {
        $this->init()
            ->write();
    }

    private function init()
    {
        $this->initPackage()
            ->initModel()
            ->initSegments();

        return $this;
    }

    private function write()
    {
        $this->migration()
            ->writeProviders();
    }

    private function migration()
    {
        if ($this->hasFile('table')) {
            (new Migration($this->choices))->handle();
        }

        return $this;
    }

    private function writeProviders()
    {
        $this->choices->get('files', new Obj())
            ->filter()->keys()
            ->intersect(array_keys($this->providers))
            ->each(fn ($file) => $this->writeProvider($file));
    }

    private function writeProvider($file)
    {
        $provider = (new $this->providers[$file]($this->choices));

        WriterFactory::make($provider)->handle();
    }

    private function initPackage()
    {
        $this->isPackage = $this->choices->filled('package')
            && $this->choices->get('package')->filled('name');

        if ($this->isPackage) {
            $this->params()->set('namespace', $this->packageNamespace());
            $this->params()->set('root', $this->packageRoot());
            $this->params()->set('rootSegment', 'src');
            $this->choices->get('files')->put('package', true);
            $segments = new Collection(explode(DIRECTORY_SEPARATOR, $this->choices->get('model')->get('name')));
            $segments->prepend('src');
            $this->choices->get('model')->put('name', $segments->implode(DIRECTORY_SEPARATOR));
        }

        Path::root($this->params()->get('root'));
        Namespacer::prefix($this->params()->get('namespace'));

        return $this;
    }

    private function initModel()
    {
        if (! $this->choices->has('model')) {
            return $this;
        }

        $model = $this->choices->get('model');
        $segments = new Collection(explode('/', $model->get('name')));

        if ($segments->first() !== 'App' && ! $this->isPackage) {
            $segments->prepend('App');
        }

        $model->set('name', Str::ucfirst($segments->pop()))
            ->set('namespace', $this->modelNamespace($segments))
            ->set('path', $this->modelPath($segments));

        return $this;
    }

    private function initSegments()
    {
        Segments::set($this->choices->get('permissionGroup'));

        return $this;
    }

    private function modelNamespace($segments)
    {
        $namespace = $this->filterNamespace($segments)->implode('\\');

        return $this->isPackage
            ? $this->packageNamespace($namespace)
            : $namespace;
    }

    private function modelPath($segments)
    {
        $segments[0] = $this->params()->get('rootSegment');

        return $segments->implode(DIRECTORY_SEPARATOR);
    }

    private function packageNamespace(?string $suffix = null)
    {
        return Collection::wrap(explode(DIRECTORY_SEPARATOR, $this->packageRoot()))
            ->pipe(fn ($segments) => $this->filterNamespace($segments))
            ->map(fn ($segment) => Str::ucfirst(Str::camel($segment)))
            ->when($suffix, fn ($segments) => $segments->push($suffix))
            ->implode('\\');
    }

    private function filterNamespace($segments)
    {
        return $segments
            ->reject(fn ($segment) => in_array($segment, ['src', 'vendor']));
    }

    private function packageRoot()
    {
        $vendor = Str::kebab($this->choices->get('package')->get('vendor'));
        $package = Str::kebab($this->choices->get('package')->get('name'));

        return Collection::wrap(['vendor', $vendor, $package])
            ->implode(DIRECTORY_SEPARATOR);
    }

    private function hasFile($file)
    {
        return $this->choices->filled('files')
            && $this->choices->get('files')->has($file);
    }

    private function params()
    {
        return $this->choices->params();
    }
}
