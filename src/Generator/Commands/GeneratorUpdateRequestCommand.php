<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorUpdateRequestCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'UpdateDummyRequest'; // Replace with the name of your update request stub
    }

    protected function getFolder(): string
    {
        return app_path("Http/Requests/{$this->model}");
    }

    protected function getFullPath(): string
    {
        return app_path("Http/Requests/{$this->model}/Update{$this->model}Request.php");
    }
}
