<?php

namespace Essa\APIToolKit\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeActionCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new action class';

    protected $type = 'Action';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return file_exists(resource_path('stubs/DummyAction.stub'))
            ? resource_path('stubs/DummyAction.stub')
            : __DIR__ . '/../Stubs/DummyAction.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Actions';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the action.'],
        ];
    }
}

