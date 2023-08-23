<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorFilterCommand extends BaseGeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummyFilters';
    }

    protected function getOutputFolder(): string
    {
        return app_path('Filters');
    }

    protected function getOutputFilePath(): string
    {
        return app_path("Filters/{$this->model}Filters.php");
    }
}
