<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorControllerCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'DummyController'; // Replace with the name of your controller stub
    }

    protected function getFolder(): string
    {
        return app_path('Http/Controllers/API');
    }

    protected function getFullPath(): string
    {
        return app_path("Http/Controllers/API/{$this->model}Controller.php");
    }
}
