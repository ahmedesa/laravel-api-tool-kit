<?php

namespace essa\APIToolKit\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use essa\APIToolKit\Generator\Generator;
use essa\APIToolKit\Generator\FileManger;

class GeneratorCommand extends Command
{
    use FileManger;

    private string     $model;
    private Filesystem $filesystem;

    protected array $all_options = [
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
    {--soft-delete}
    ';

    protected $description = 'This command generate api crud.';

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    public function handle()
    {
        if ($this->isReservedName($this->argument('model'))) {
            $this->error('The name "' . $this->argument('model') . '" is reserved by PHP.');

            return false;
        }

        $model = ucfirst($this->argument('model'));

        $this->model = $model;

        $this->getUserChoices();

        $this->createModel();

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

        if ($this->option('routes')) {
            $this->filesystem->append(
                base_path('routes/api.php'),
                $this->getTemplate('routes')
            );
        }

        $this->info('Module created successfully!');
    }

    private function createController(): void
    {
        if (! file_exists(app_path("/Http/Controllers/API"))) {
            $this->filesystem->makeDirectory(app_path("/Http/Controllers/API"));
        }

        file_put_contents(app_path("Http/Controllers/API/{$this->model}Controller.php"), $this->getTemplate('DummyController'));
    }

    private function createModel(): void
    {
        file_put_contents(app_path("Models/{$this->model}.php"), $this->getTemplate('Dummy'));
    }

    private function createTest(): void
    {
        file_put_contents(base_path("tests/Feature/{$this->model}Test.php"), $this->getTemplate('DummyTest'));
    }

    private function createFilter(): void
    {
        if (! file_exists(app_path("/Filters"))) {
            $this->filesystem->makeDirectory(app_path("/Filters"));
        }

        file_put_contents(app_path("Filters/{$this->model}Filters.php"), $this->getTemplate('DummyFilters'));
    }

    private function createResources(): void
    {
        if (! file_exists(app_path("/Http/Resources"))) {
            $this->filesystem->makeDirectory(app_path("/Http/Resources"));
        }

        if (! file_exists(app_path("/Http/Resources/" . $this->model))) {
            $this->filesystem->makeDirectory(app_path("/Http/Resources/" . $this->model));
        }

        file_put_contents(
            app_path("Http/Resources/{$this->model}/{$this->model}Resource.php"),
            $this->getTemplate('DummyResource')
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
            'name' => $this->model . '\Create' . $this->model . 'Request',
        ]);

        Artisan::call('make:request', [
            'name' => $this->model . '\Update' . $this->model . 'Request',
        ]);
    }

    private function createSeeder(): void
    {
        Artisan::call('make:seeder', [
            'name' => $this->model . 'Seeder',
        ]);
    }

    private function isReservedName($name): bool
    {
        $name = strtolower($name);

        return in_array($name, $this->reservedNames);
    }

    private function getUserChoices(): void
    {
        $yes_or_no = [
            "y" => 'Yes',
            "n" => 'No',
        ];

        $all_default_selected = $this->choice(
            "Select all default options :\n "
            . implode(",", config('api-tool-kit.default_generates'))
            . "?",
            $yes_or_no,
            'y'
        );

        $choice = $this->choice(
            "Do you want to use <options=bold>soft delete</> ?",
            $yes_or_no,
            'y'
        );

        $this->input->setOption('soft-delete', $choice == 'y');

        if ($all_default_selected == 'y') {
            foreach (config('api-tool-kit.default_generates') as $option) {
                if (in_array($option, $this->all_options)) {
                    $this->input->setOption($option, true);

                }
            }

            return;
        }

        foreach ($this->all_options as $option) {
            $choice = $this->choice(
                "Do you want to generate <options=bold>{$option}</> ?",
                $yes_or_no,
                'y'
            );

            $this->input->setOption($option, $choice == 'y');
        }
    }
}
