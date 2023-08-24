<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Essa\APIToolKit\Generator\PathResolver\SeedPathResolver;

class SeederGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummySeeder';
    }

    protected function getOutputFilePath(): PathResolverInterface
    {
        return new SeedPathResolver($this->generationConfiguration->getModel());
    }
}
