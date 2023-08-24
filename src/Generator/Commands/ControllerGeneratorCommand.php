<?php

namespace Essa\APIToolKit\Generator\Commands;

class ControllerGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummyController';
    }

    protected function getOutputFolder(): string
    {
        return app_path('Http/Controllers/API');
    }

    protected function getOutputFilePath(): string
    {
        return app_path("Http/Controllers/API/{$this->generationConfiguration->getModel()}Controller.php");
    }
}
