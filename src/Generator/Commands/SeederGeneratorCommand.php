<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Essa\APIToolKit\Generator\PathResolver\SeederPathResolver;

class SeederGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummySeeder';
    }

    protected function getOutputFilePath(): PathResolverInterface
    {
        return new SeederPathResolver($this->generationConfiguration->getModel());
    }
}
