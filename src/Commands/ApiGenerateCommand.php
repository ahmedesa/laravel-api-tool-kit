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

    private array $defaultCommands = ['model'];

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

        $userChoices = $this->getUserChoices();

        $schemaDefinition = SchemaDefinition::createFromSchemaString($this->argument('schema'));

        $apiGenerationCommandInputs = new ApiGenerationCommandInputs($model, $userChoices, $schemaDefinition);

        $this->executeCommands($apiGenerationCommandInputs);

        $this->info('Here is your schema : ');

        $table = new SchemaConsoleTable($schemaDefinition);

        $this->displayTable($table->generate());

        $this->info('Generated Files for Model:');

        $table = new GeneratedFilesConsoleTable($apiGenerationCommandInputs);

        $this->displayTable($table->generate());
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

    private function executeCommands(ApiGenerationCommandInputs $apiGenerationCommandInputs): void
    {
        $commandDefinitions = config('api-tool-kit.api_generators.commands');

        foreach ($commandDefinitions as $definition) {
            if ($this->shouldExecute($definition['option'])) {
                $this->container
                    ->get($definition['command'])
                    ->run($apiGenerationCommandInputs);
            }
        }
    }

    private function displayTable(TableDate $output): void
    {
        $this->table($output->getHeaders(), $output->getTableData());
    }

    private function shouldExecute(string $option): bool
    {
        return in_array($option, $this->defaultCommands) || $this->option($option);
    }
}
