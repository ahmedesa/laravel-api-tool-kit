<?php

namespace Essa\APIToolKit\Generator\Handlers;

use Essa\APIToolKit\Generator\StubParser;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ComponentCreatorHandler
{
    private Filesystem $filesystem;
    private string $model;
    private array $userChoices;
    private StubParser $stubParser;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function handle(): void
    {
        $this->createModel();

        if ($this->userChoices['controller']) {
            $this->createController();
        }

        if ($this->userChoices['filter']) {
            $this->createFilter();
        }

        if ($this->userChoices['resource']) {
            $this->createResources();
        }

        if ($this->userChoices['test']) {
            $this->createTest();
        }

        if ($this->userChoices['migration']) {
            $this->createMigration();
        }

        if ($this->userChoices['factory']) {
            $this->createFactory();
        }

        if ($this->userChoices['request']) {
            $this->createRequest();
        }

        if ($this->userChoices['seeder']) {
            $this->createSeeder();
        }

        $this->updateRoutes();
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function setUserChoices(array $userChoices): self
    {
        $this->userChoices = $userChoices;

        return $this;
    }

    public function setStubParser(StubParser $stubParser): self
    {
        $this->stubParser = $stubParser;

        return $this;
    }

    private function createController(): void
    {
        if (! file_exists(app_path('/Http/Controllers/API'))) {
            $this->filesystem->makeDirectory(app_path('/Http/Controllers/API'));
        }

        file_put_contents(app_path("Http/Controllers/API/{$this->model}Controller.php"), $this->stubParser->parseStub('DummyController'));
    }

    private function createModel(): void
    {
        file_put_contents(app_path("Models/{$this->model}.php"), $this->stubParser->parseStub('Dummy'));
    }

    private function createTest(): void
    {
        if (! file_exists(base_path('tests/Feature/'))) {
            $this->filesystem->makeDirectory(base_path('tests/Feature/'));
        }

        file_put_contents(base_path("tests/Feature/{$this->model}Test.php"), $this->stubParser->parseStub('DummyTest'));
    }

    private function createFilter(): void
    {
        if (! file_exists(app_path('/Filters'))) {
            $this->filesystem->makeDirectory(app_path('/Filters'));
        }

        file_put_contents(app_path("Filters/{$this->model}Filters.php"), $this->stubParser->parseStub('DummyFilters'));
    }

    private function createResources(): void
    {
        if (! file_exists(app_path('/Http/Resources'))) {
            $this->filesystem->makeDirectory(app_path('/Http/Resources'));
        }

        if (! file_exists(app_path('/Http/Resources/' . $this->model))) {
            $this->filesystem->makeDirectory(app_path('/Http/Resources/' . $this->model));
        }

        file_put_contents(
            app_path("Http/Resources/{$this->model}/{$this->model}Resource.php"),
            $this->stubParser->parseStub('DummyResource')
        );
    }

    private function createMigration(): void
    {
        Artisan::call('make:migration', [
            'name' => 'create_' . Str::plural(Str::snake($this->model)) . '_table',
        ]);
    }

    private function createFactory(): void
    {
        Artisan::call('make:factory', [
            'name' => $this->model . 'Factory',
            '--model' => $this->model,
        ]);
    }

    private function createRequest(): void
    {
        Artisan::call('make:request', [
            'name' => "{$this->model}\Create{$this->model}Request",
        ]);

        Artisan::call('make:request', [
            'name' => "{$this->model}\Update{$this->model}Request",
        ]);
    }

    private function createSeeder(): void
    {
        Artisan::call('make:seeder', [
            'name' => $this->model . 'Seeder',
        ]);
    }

    private function updateRoutes(): void
    {
        if ($this->userChoices['routes']) {
            $this->filesystem->append(
                base_path('routes/api.php'),
                $this->stubParser->parseStub('routes')
            );
        }
    }
}
