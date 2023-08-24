<?php

namespace Essa\APIToolKit\Generator\Commands;

use Illuminate\Filesystem\Filesystem;

class RoutesGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'routes';
    }

    protected function getOutputFolderPath(): string
    {
        return base_path('routes/api.php');
    }

    protected function getOutputFileName(): string
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
