<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Essa\APIToolKit\Generator\PathResolver\TestPathResolver;

class TestGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummyTest';
    }

    protected function getOutputFilePath(): PathResolverInterface
    {
        return new TestPathResolver($this->generationConfiguration->getModel());
    }
}
