<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorFilterCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'DummyFilters'; // Replace with the name of your filter stub
    }

    protected function getFolder(): string
    {
        return app_path('Filters');
    }

    protected function getFullPath(): string
    {
        return app_path("Filters/{$this->model}Filters.php");
    }
}
