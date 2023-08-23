<?php

namespace Essa\APIToolKit\Commands;

use Essa\APIToolKit\Generator\Commands\GeneratorControllerCommand;
use Essa\APIToolKit\Generator\Commands\GeneratorCreateRequestCommand;
use Essa\APIToolKit\Generator\Commands\GeneratorFactoryCommand;
use Essa\APIToolKit\Generator\Commands\GeneratorFilterCommand;
use Essa\APIToolKit\Generator\Commands\GeneratorMigrationCommand;
use Essa\APIToolKit\Generator\Commands\GeneratorModelCommand;
use Essa\APIToolKit\Generator\Commands\GeneratorResourceCommand;
use Essa\APIToolKit\Generator\Commands\GeneratorRoutesCommand;
use Essa\APIToolKit\Generator\Commands\GeneratorSeederCommand;
use Essa\APIToolKit\Generator\Commands\GeneratorTestCommand;
use Essa\APIToolKit\Generator\Commands\GeneratorUpdateRequestCommand;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GeneratorCommand extends Command
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
    public function handle()
    {
        $model = ucfirst($this->argument('model'));

        if ($this->isReservedName($this->argument('model'))) {
            $this->error('The name "' . $this->argument('model') . '" is reserved by PHP.');

            return false;
        }

        $userChoices = $this->getUserChoices();

        $schema = $this->getSchema();

        $this->generateModules($model, $userChoices, $schema);

        $this->info('Module created successfully!');
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
            ['routes', null, InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, policy, resource controller, and form request classes for the model'],
            ['soft-delete', null, InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, policy, resource controller, and form request classes for the model'],
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['filter', 'F', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['test', 't', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
            ['seeder', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Indicates if the generated controller should be a resource controller'],
            ['request', 'R', InputOption::VALUE_NONE, 'Create new form request classes and use them in the resource controller'],
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

    private function getSchema(): ?array
    {
        if ( ! $this->argument('schema')) {
            return null;
        }
        return explode(',', $this->argument('schema'));
    }

    private function generateModules(string $model, array $userChoices, ?array $schema): void
    {
        $generator = new GeneratorModelCommand(
            model: $model,
            options: $userChoices,
            schema: $schema
        );

        $generator->handle();

        if ($this->option('factory')) {
            $generator = new GeneratorFactoryCommand(
                model: $model,
                options: $userChoices,
                schema: $schema
            );

            $generator->handle();
        }

        if ($this->option('seeder')) {
            $generator = new GeneratorSeederCommand(
                model: $model,
                options: $userChoices,
                schema: $schema
            );

            $generator->handle();
        }

        if ($this->option('controller')) {
            $generator = new GeneratorControllerCommand(
                model: $model,
                options: $userChoices,
                schema: $schema
            );

            $generator->handle();
        }

        if ($this->option('test')) {
            $generator = new GeneratorTestCommand(
                model: $model,
                options: $userChoices,
                schema: $schema
            );

            $generator->handle();
        }

        if ($this->option('resource')) {
            $generator = new GeneratorResourceCommand(
                model: $model,
                options: $userChoices,
                schema: $schema
            );

            $generator->handle();
        }

        if ($this->option('request')) {
            $generator = new GeneratorCreateRequestCommand(
                model: $model,
                options: $userChoices,
                schema: $schema
            );

            $generator->handle();

            $generator = new GeneratorUpdateRequestCommand(
                model: $model,
                options: $userChoices,
                schema: $schema
            );

            $generator->handle();
        }

        if ($this->option('filter')) {
            $generator = new GeneratorFilterCommand(
                model: $model,
                options: $userChoices,
                schema: $schema
            );

            $generator->handle();
        }

        if ($this->option('migration')) {
            $generator = new GeneratorMigrationCommand(
                model: $model,
                options: $userChoices,
                schema: $schema
            );

            $generator->handle();
        }

        if ($this->option('routes')) {
            $generator = new GeneratorRoutesCommand(
                model: $model,
                options: $userChoices,
                schema: $schema
            );

            $generator->handle();
        }
    }
}
