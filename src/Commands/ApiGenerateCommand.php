<?php

namespace Essa\APIToolKit\Commands;

use Essa\APIToolKit\Generator\ConsoleTable\GeneratedFilesConsoleTable;
use Essa\APIToolKit\Generator\ConsoleTable\SchemaConsoleTable;
use Essa\APIToolKit\Generator\DTOs\ApiGenerationCommandInputs;
use Essa\APIToolKit\Generator\DTOs\SchemaDefinition;
use Essa\APIToolKit\Generator\DTOs\TableDate;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ApiGenerateCommand extends Command
{
    protected $name = 'api:generate';

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

    public function __construct(private Container $container)
    {
        parent::__construct();
    }

    public function handle()
    {
        $model = ucfirst($this->argument('model'));

        if ($this->isReservedName($this->argument('model'))) {
            $this->error('The name "' . $this->argument('model') . '" is reserved by PHP.');

            return false;
        }

        $apiGenerationCommandInputs = new ApiGenerationCommandInputs(
            model: $model,
            userChoices: $this->getUserChoices(),
            schema: SchemaDefinition::createFromSchemaString($this->argument('schema'))
        );

        $this->executeCommands($apiGenerationCommandInputs);

        $this->info('Here is your schema : ');

        $table = new SchemaConsoleTable();

        $this->displayTable($table->generate($apiGenerationCommandInputs));

        $this->info('Generated Files : ');

        $table = new GeneratedFilesConsoleTable();

        $this->displayTable($table->generate($apiGenerationCommandInputs));
    }


    protected function getArguments(): array
    {
        return [
            ['model', InputArgument::REQUIRED, 'The model.'],
            ['schema', InputArgument::OPTIONAL, 'The schema.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['all', null, InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, policy, resource controller, and form request classes for the model'],
            ['routes', null, InputOption::VALUE_NONE, 'Generate routes for the crud operations'],
            ['soft-delete', null, InputOption::VALUE_NONE, 'Generate soft delete functionality for the model'],
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['filter', 'F', InputOption::VALUE_NONE, 'Create a new filter for the model'],
            ['test', 't', InputOption::VALUE_NONE, 'Create new test cases for the model'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
            ['seeder', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Generate a resource controller for the model'],
            ['request', 'R', InputOption::VALUE_NONE, 'Create new form request classes for the model and use them in the resource controller'],
        ];
    }

    private function getUserChoices(): array
    {
        if ($this->option('all')) {
            $this->setDefaultOptions();
        }

        $extraOptions = [
            'update-request' => $this->option('request'),
            'create-request' => $this->option('request'),
            'model' => true
        ];

        return $this->options() + $extraOptions;
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

    private function executeCommands(ApiGenerationCommandInputs $apiGenerationCommandInputs): void
    {
        $apiGeneratorOptions = config('api-tool-kit.api_generators.options');

        foreach ($apiGeneratorOptions as $option => $config) {
            if ($apiGenerationCommandInputs->isOptionSelected($option)) {
                $this->container
                    ->get($config['command'])
                    ->run($apiGenerationCommandInputs);
            }
        }
    }

    private function displayTable(TableDate $output): void
    {
        $this->table($output->getHeaders(), $output->getTableData());
    }
}
