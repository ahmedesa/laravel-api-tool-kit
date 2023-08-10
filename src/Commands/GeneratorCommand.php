<?php

namespace Essa\APIToolKit\Commands;

use Essa\APIToolKit\Generator\StubParser;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class GeneratorCommand extends Command
{
    protected array $allOptions = [
        'controller',
        'request',
        'resource',
        'migration',
        'factory',
        'seeder',
        'filter',
        'test',
        'routes',
    ];

    protected array $reservedNames = [
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

    protected $signature = 'api:generate {model}
                            {--m|migration}
                            {--c|controller}
                            {--R|request}
                            {--r|resource}
                            {--s|seeder}
                            {--f|factory}
                            {--F|filter}
                            {--t|test}
                            {--routes}
                            {--soft-delete}';

    protected $description = 'This command generate api crud.';

    private string $model;
    private Filesystem $filesystem;
    private StubParser $stubParser;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    public function handle()
    {
        $model = ucfirst($this->argument('model'));

        if ($this->isReservedName($this->argument('model'))) {
            $this->error('The name "' . $this->argument('model') . '" is reserved by PHP.');

            return false;
        }

        $this->model = $model;

        $this->getUserChoices();

        $this->stubParser = new StubParser($model, $this->options());

        $this->createModel();

        $this->createComponents();

        $this->updateRoutes();

        $this->info('Module created successfully!');
    }

    private function createComponents(): void
    {
        if ($this->option('controller')) {
            $this->createController();
        }

        if ($this->option('filter')) {
            $this->createFilter();
        }

        if ($this->option('resource')) {
            $this->createResources();
        }

        if ($this->option('test')) {
            $this->createTest();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('factory')) {
            $this->createFactory();
        }

        if ($this->option('request')) {
            $this->createRequest();
        }

        if ($this->option('seeder')) {
            $this->createSeeder();
        }
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
        if ($this->option('routes')) {
            $this->filesystem->append(
                base_path('routes/api.php'),
                $this->stubParser->parseStub('routes')
            );
        }
    }

    private function isReservedName($name): bool
    {
        $name = strtolower($name);

        return in_array($name, $this->reservedNames);
    }

    private function getUserChoices(): void
    {
        $yesOrNo = [
            'y' => 'Yes',
            'n' => 'No',
        ];

        $allDefaultSelected = $this->choice(
            'Select all default options ?',
            $yesOrNo,
            'y'
        );

        $choice = $this->choice(
            'Do you want to use <options=bold>soft delete</> ?',
            $yesOrNo,
            'y'
        );

        $this->input->setOption('soft-delete', $choice == 'y');

        if ($allDefaultSelected == 'y') {
            $this->setDefaultOptions();

            return;
        }

        foreach ($this->allOptions as $option) {
            $choice = $this->choice(
                "Do you want to generate <options=bold>{$option}</> ?",
                $yesOrNo,
                'y'
            );

            $this->input->setOption($option, $choice == 'y');
        }
    }

    private function setDefaultOptions(): void
    {
        foreach (config('api-tool-kit.default_generates') as $option) {
            if (in_array($option, $this->allOptions)) {
                $this->input->setOption($option, true);
            }
        }
    }
}
