<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorTestCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'DummyTest'; // Replace with the name of your test stub
    }

    protected function getFolder(): string
    {
        return base_path('tests/Feature');
    }

    protected function getFullPath(): string
    {
        return base_path("tests/Feature/{$this->model}Test.php");
    }
}
