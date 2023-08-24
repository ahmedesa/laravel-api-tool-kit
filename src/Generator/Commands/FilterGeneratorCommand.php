<?php

namespace Essa\APIToolKit\Generator\Commands;

class FilterGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummyFilters';
    }

    protected function getOutputFolderPath(): string
    {
        return app_path('Filters');
    }

    protected function getOutputFileName(): string
    {
        return "{$this->generationConfiguration->getModel()}Filters.php";
    }
}
