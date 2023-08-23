<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorModelCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'Dummy';
    }

    protected function getFolder(): string
    {
        return app_path('/Models');
    }

    protected function getFullPath(): string
    {
        return app_path("Models/{$this->model}.php");
    }
}
