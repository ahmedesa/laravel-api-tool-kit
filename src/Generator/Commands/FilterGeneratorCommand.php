<?php

namespace Essa\APIToolKit\Generator\Commands;

class FilterGeneratorCommand extends GeneratorCommand
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
        return app_path("Filters/{$this->generationConfiguration->getModel()}Filters.php");
    }
}
