<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorRequestCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'CreateDummyRequest'; // Replace with the name of your request stub
    }

    protected function getFolder(): string
    {
        return app_path("Http/Requests/{$this->model}");
    }

    protected function getFullPath(): string
    {
        return app_path("Http/Requests/{$this->model}/Create{$this->model}Request.php");
    }
}
