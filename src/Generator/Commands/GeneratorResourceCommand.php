<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorResourceCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'DummyResource'; // Replace with the name of your resource stub
    }

    protected function getFolder(): string
    {
        return app_path("Http/Resources/{$this->model}");
    }

    protected function getFullPath(): string
    {
        return app_path("Http/Resources/{$this->model}/{$this->model}Resource.php");
    }
}
