<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Illuminate\Filesystem\Filesystem;

class GeneratorRoutesCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'routes'; // Replace with the name of your controller stub
    }

    protected function getFolder(): string
    {
        return base_path('routes/api.php');
    }

    protected function getFullPath(): string
    {
        return base_path('routes/api.php');
    }

    protected function saveContentInTheFilePath(): void
    {
        app(Filesystem::class)->append(
            base_path('routes/api.php'),
            $this->parseStub('routes')
        );
    }
}
