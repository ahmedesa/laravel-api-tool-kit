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

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function handle(): void
    {
        $this->generateArtisanCommands();

        $this->generateTheComponents();
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

    private function generateArtisanCommands(): void
    {
        $commandsToGenerate = $this->commandsToGenerate();

        foreach ($commandsToGenerate as $commands) {

            if ($this->userChoices[$commands->type]) {
                Artisan::call($commands->name, $commands->options);
            }
        }
    }

    private function generateTheComponents(): void
    {
        $stubParser = new StubParser($this->model, $this->userChoices);

        $componentsToGenerate = $this->componentsToGenerate();

        foreach ($componentsToGenerate as $component) {
            if ('model' === $component->type || $this->userChoices[$component->type]) {
                $this->createComponent($component, $stubParser);
            }
        }

        if ($this->userChoices['routes']) {
            $this->appendRoutes($stubParser);
        }
    }

    private function appendRoutes(StubParser $stubParser): void
    {
        $this->filesystem->append(
            base_path('routes/api.php'),
            $stubParser->parseStub('routes')
        );
    }

    private function createComponent(ComponentInfo $component, StubParser $stubParser): void
    {
        if ( ! file_exists($component->folder)) {
            $this->filesystem->makeDirectory($component->folder, 0777, true, true);
        }

        file_put_contents($component->path, $stubParser->parseStub($component->stub));
    }

    private function commandsToGenerate(): array
    {
        return [
            new CommandInfo(
                'migration',
                'make:migration',
                [
                    'name' => 'create_' . Str::plural(Str::snake($this->model)) . '_table',
                ]
            ),
            new CommandInfo(
                'factory',
                'make:factory',
                [
                    'name' => "{$this->model}Factory",
                    '--model' => $this->model,
                ]
            ),
            new CommandInfo(
                'seeder',
                'make:seeder',
                [
                    'name' => "{$this->model}Seeder",
                ]
            ),
            new CommandInfo(
                'request',
                'make:request',
                [
                    'name' => "{$this->model}\Create{$this->model}Request",
                ]
            ),
            new CommandInfo(
                'request',
                'make:request',
                [
                    'name' => "{$this->model}\Update{$this->model}Request",
                ]
            ),
        ];
    }
    private function componentsToGenerate(): array
    {
        return [
            new ComponentInfo(
                'controller',
                app_path('/Http/Controllers/API'),
                app_path("Http/Controllers/API/{$this->model}Controller.php"),
                'DummyController'
            ),
            new ComponentInfo(
                'test',
                base_path('tests/Feature/'),
                base_path("tests/Feature/{$this->model}Test.php"),
                'DummyTest'
            ),
            new ComponentInfo(
                'filter',
                app_path('/Filters'),
                app_path("Filters/{$this->model}Filters.php"),
                'DummyFilters'
            ),
            new ComponentInfo(
                'resource',
                app_path('/Http/Resources/' . $this->model),
                app_path("Http/Resources/{$this->model}/{$this->model}Resource.php"),
                'DummyResource'
            ),
            new ComponentInfo(
                'model',
                app_path('/Models'),
                app_path("Models/{$this->model}.php"),
                'Dummy'
            ),
        ];
    }
}
