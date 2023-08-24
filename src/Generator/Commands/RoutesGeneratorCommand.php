<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Essa\APIToolKit\Generator\PathResolver\RoutesPathResolver;
use Illuminate\Filesystem\Filesystem;

class RoutesGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'routes';
    }

    protected function getOutputFilePath(): PathResolverInterface
    {
        return new RoutesPathResolver($this->generationConfiguration->getModel());
    }

    protected function saveContentToFile(): void
    {
        app(Filesystem::class)->append(
            base_path('routes/api.php'),
            $this->parseStub('routes')
        );
    }
}
