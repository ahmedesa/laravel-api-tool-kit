<?php

namespace Essa\APIToolKit\Generator\Commands;

class RoutesGeneratorCommand extends GeneratorCommand
{
    protected string $type = 'routes';

    protected function getStubName(): string
    {
        return 'DummyRoutes';
    }

    protected function saveContentToFile(): void
    {
        $this->filesystem->append(
            base_path('routes/api.php'),
            $this->parseStub('DummyRoutes')
        );
    }
}
