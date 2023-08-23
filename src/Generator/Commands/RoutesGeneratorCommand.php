<?php

namespace Essa\APIToolKit\Generator\Commands;

use Illuminate\Filesystem\Filesystem;

class RoutesGeneratorCommand extends BaseGeneratorCommand
{
    protected function getStubName(): string
    {
        return 'routes';
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
