<?php

namespace Essa\APIToolKit\Generator\Commands;

class ControllerGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummyController';
    }

    protected function getOutputFolderPath(): string
    {
        return app_path('Http/Controllers/API');
    }

    protected function getOutputFileName(): string
    {
        return "{$this->generationConfiguration->getModel()}Controller.php";
    }
}
