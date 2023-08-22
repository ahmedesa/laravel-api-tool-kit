<?php

namespace Essa\APIToolKit\Commands;

use Essa\APIToolKit\Generator\Handlers\ComponentCreatorHandler;
use Essa\APIToolKit\Generator\SchemaParser;
use Illuminate\Console\Command;

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

    private ComponentCreatorHandler $componentCreatorHandler;

    public function __construct(
        ComponentCreatorHandler $componentCreatorHandler
    )
    {
        parent::__construct();

        $this->componentCreatorHandler = $componentCreatorHandler;
    }

    public function handle()
    {
        $model = ucfirst($this->argument('model'));

        if ($this->isReservedName($this->argument('model'))) {
            $this->error('The name "' . $this->argument('model') . '" is reserved by PHP.');

            return false;
        }

        $userChoices = $this->getUserChoices();

        $schemaParser = new SchemaParser($this->argument('schema'));

        $schemaParserOutput = $schemaParser->parse();

        $this->componentCreatorHandler
            ->setModel($model)
            ->setUserChoices($userChoices)
            ->setSchemaParserOutput($schemaParserOutput)
            ->handle();

        $this->info('Module created successfully!');
    }

    private function getUserChoices(): array
    {
        if ($this->option('all')) {
            $this->setDefaultOptions();
        }

        return $this->options() + ['soft-delete' => $this->option('soft-delete')];
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
}
