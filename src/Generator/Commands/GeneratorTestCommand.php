<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorTestCommand extends BaseGeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummyTest';
    }

    protected function getOutputFolder(): string
    {
        return base_path('tests/Feature');
    }

    protected function getOutputFilePath(): string
    {
        return base_path("tests/Feature/{$this->model}Test.php");
    }
}
