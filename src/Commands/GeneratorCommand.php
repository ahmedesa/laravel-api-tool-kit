<?php

namespace Essa\APIToolKit\Commands;

use Essa\APIToolKit\Generator\Handlers\ComponentCreatorHandler;
use Essa\APIToolKit\Generator\Handlers\UserChoicesHandler;
use Illuminate\Console\Command;

class GeneratorCommand extends Command
{
    protected $signature = 'api:generate {model}
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

    private UserChoicesHandler $userChoicesHandler;

    private ComponentCreatorHandler $componentCreatorHandler;

    public function __construct(
        UserChoicesHandler $userChoicesHandler,
        ComponentCreatorHandler $componentCreatorHandler
    ) {
        parent::__construct();

        $this->userChoicesHandler = $userChoicesHandler;
        $this->componentCreatorHandler = $componentCreatorHandler;
    }

    public function handle()
    {
        $model = ucfirst($this->argument('model'));

        if ($this->isReservedName($this->argument('model'))) {
            $this->error('The name "' . $this->argument('model') . '" is reserved by PHP.');

            return false;
        }

        $userChoices = $this->userChoicesHandler
            ->setCommand($this)
            ->handel();

        $this->componentCreatorHandler
            ->setModel($model)
            ->setUserChoices($userChoices)
            ->handle();

        $this->info('Module created successfully!');
    }

    private function isReservedName($name): bool
    {
        return in_array(mb_strtolower($name), $this->reservedNames);
    }
}
