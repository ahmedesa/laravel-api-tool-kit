<?php

namespace Essa\APIToolKit\Generator\Handlers;

use Essa\APIToolKit\Generator\DTOs\ComponentInfo;
use Essa\APIToolKit\Generator\DTOs\SchemaParserOutput;
use Essa\APIToolKit\Generator\StubParser;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ComponentCreatorHandler
{
    private string $model;
    private array $userChoices;
    private SchemaParserOutput $schemaParserOutput;

    public function __construct(private Filesystem $filesystem)
    {
    }

    public function handle(): void
    {
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

    public function getSchemaParserOutput(): SchemaParserOutput
    {
        return $this->schemaParserOutput;
    }

    public function setSchemaParserOutput(SchemaParserOutput $schemaParserOutput): self
    {
        $this->schemaParserOutput = $schemaParserOutput;

        return $this;
    }

    private function generateTheComponents(): void
    {
        $stubParser = new StubParser($this->model, $this->userChoices, $this->schemaParserOutput);

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

    private function componentsToGenerate(): array
    {
        $migrationFileName = $this->getMigrationTableName();

        return [
            new ComponentInfo(
                type: 'model',
                folder: app_path('/Models'),
                path: app_path("Models/{$this->model}.php"),
                stub: 'Dummy'
            ),
            new ComponentInfo(
                type: 'factory',
                folder: database_path('/factories'),
                path: database_path("factories/{$this->model}Factory.php"),
                stub: 'DummyFactory'
            ),
            new ComponentInfo(
                type: 'seeder',
                folder: database_path('/seeders'),
                path: database_path("seeders/{$this->model}Seeder.php"),
                stub: 'DummySeeder'
            ),
            new ComponentInfo(
                type: 'migration',
                folder: database_path('/migrations'),
                path: database_path("migrations/{$migrationFileName}"),
                stub: 'dummy_migration'
            ),
            new ComponentInfo(
                type: 'controller',
                folder: app_path('/Http/Controllers/API'),
                path: app_path("Http/Controllers/API/{$this->model}Controller.php"),
                stub: 'DummyController'
            ),
            new ComponentInfo(
                type: 'test',
                folder: base_path('tests/Feature/'),
                path: base_path("tests/Feature/{$this->model}Test.php"),
                stub: 'DummyTest'
            ),
            new ComponentInfo(
                type: 'filter',
                folder: app_path('/Filters'),
                path: app_path("Filters/{$this->model}Filters.php"),
                stub: 'DummyFilters'
            ),
            new ComponentInfo(
                type: 'resource',
                folder: app_path('/Http/Resources/' . $this->model),
                path: app_path("Http/Resources/{$this->model}/{$this->model}Resource.php"),
                stub: 'DummyResource'
            ),
            new ComponentInfo(
                type: 'request',
                folder: app_path('/Http/Requests/' . $this->model),
                path: app_path("Http/Requests/{$this->model}/Create{$this->model}Request.php"),
                stub: 'CreateDummyRequest'
            ),
            new ComponentInfo(
                type: 'request',
                folder: app_path('/Http/Requests/' . $this->model),
                path: app_path("Http/Requests/{$this->model}/Update{$this->model}Request.php"),
                stub: 'UpdateDummyRequest'
            ),
        ];
    }

    private function getMigrationTableName(): string
    {
        $migrationClass = 'create_' . Str::plural(Str::snake($this->model)) . '_table';

        return date('Y_m_d_His') . "_{$migrationClass}.php";
    }
}
