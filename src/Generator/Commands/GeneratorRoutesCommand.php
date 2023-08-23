<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Illuminate\Filesystem\Filesystem;

class GeneratorRoutesCommand extends BaseGeneratorCommand
{
    protected function getStubName(): string
    {
        return 'routes'; // Replace with the name of your controller stub
    }

    protected function getOutputFolder(): string
    {
        return base_path('routes/api.php');
    }

    protected function getOutputFilePath(): string
    {
        return base_path('routes/api.php');
    }

    protected function saveContentToFile(): void
    {
        app(Filesystem::class)->append(
            base_path('routes/api.php'),
            $this->parseStub('routes')
        );
    }
}
