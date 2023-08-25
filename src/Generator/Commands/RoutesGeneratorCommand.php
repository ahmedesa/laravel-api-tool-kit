<?php

namespace Essa\APIToolKit\Generator\Commands;

use Illuminate\Filesystem\Filesystem;

class RoutesGeneratorCommand extends GeneratorCommand
{
    protected string $type = 'routes';

    protected function getStubName(): string
    {
        return 'DummyRoutes';
    }

    protected function saveContentToFile(): void
    {
        app(Filesystem::class)->append(
            base_path('routes/api.php'),
            $this->parseStub('DummyRoutes')
        );
    }
}
