<?php

namespace Essa\APIToolKit\Generator\Commands;

class TestGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummyTest';
    }

    protected function getOutputFolderPath(): string
    {
        return base_path('tests/Feature');
    }

    protected function getOutputFileName(): string
    {
        return "{$this->generationConfiguration->getModel()}Test.php";
    }
}
