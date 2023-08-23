<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorFactoryCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'DummyFactory'; // Replace with the name of your factory stub
    }

    protected function getFolder(): string
    {
        return database_path('/factories');
    }

    protected function getFullPath(): string
    {
        return database_path("factories/{$this->model}Factory.php");
    }
}
