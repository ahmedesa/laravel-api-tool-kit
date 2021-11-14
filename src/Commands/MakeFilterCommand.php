<?php

namespace Essa\APIToolKit\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeFilterCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new filter class';

    protected $type = 'Filter';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return file_exists(resource_path('stubs/DummyFilters.stub'))
            ? resource_path('stubs/DummyFilters.stub')
            : __DIR__ . '/../Stubs/DummyFilters.stub';
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
        return $rootNamespace . '\Filters';
    }

    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(['DummyFilters', '{{ class }}', '{{class}}'], $class, $stub);
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $searches = [
            ['App\Filters']
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel()],
                $stub
            );
        }

        return $this;
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

