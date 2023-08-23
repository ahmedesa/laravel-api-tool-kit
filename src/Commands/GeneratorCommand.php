<?php

namespace Essa\APIToolKit\Commands;

use Essa\APIToolKit\Generator\DTOs\ComponentInfo;
use Essa\APIToolKit\Generator\SchemaParser;
use Essa\APIToolKit\Generator\StubParser;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class GeneratorCommand extends Command
{
    protected $signature = 'api:generate {model} {schema?}
                            {--m|migration}
                            {--c|controller}
                            {--R|request}
                            {--r|resource}
                            {--s|seeder}
                            {--f|factory}
                            {--F|filter}
                            {--t|test}
                            {--all}
                            {--routes}
                            {--soft-delete}';

    protected $description = 'This command generate api crud.';

    private array $reservedNames = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'finally',
        'fn',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        'yield',
    ];

    private const DEFAULT_VALUES = [
        'model',
    ];

    public function __construct(
        private Filesystem $filesystem,
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $model = ucfirst($this->argument('model'));

        $this->model = ucfirst($this->argument('model'));

        if ($this->isReservedName($this->argument('model'))) {
            $this->error('The name "' . $this->argument('model') . '" is reserved by PHP.');

            return false;
        }

        $userChoices = $this->getUserChoices();

        $schemaParser = new SchemaParser($this->argument('schema'));

        $schemaParserOutput = $schemaParser->parse();

        $stubParser = new StubParser(
            model: $model,
            options: $userChoices,
            schemaParserOutput: $schemaParserOutput
        );

        $componentsToGenerate = $this->componentsToGenerate();

        foreach ($componentsToGenerate as $component) {
            if (in_array($component->type, self::DEFAULT_VALUES) || $userChoices[$component->type]) {
                $this->createComponent($component, $stubParser);
            }
        }

        if ($this->option('routes')) {
            $this->appendRoutes($stubParser);
        }

        $this->info('Module created successfully!');
    }

    private function getUserChoices(): array
    {
        if ($this->option('all')) {
            $this->setDefaultOptions();
        }

        return $this->options();
    }

    private function setDefaultOptions(): void
    {
        $defaultOptions = config('api-tool-kit.default_generates');

        foreach ($defaultOptions as $option) {
            $this->input->setOption($option, true);
        }
    }

    private function isReservedName($name): bool
    {
        return in_array(mb_strtolower($name), $this->reservedNames);
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
        if (!file_exists($component->folder)) {
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
