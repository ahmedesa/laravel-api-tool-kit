<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Essa\APIToolKit\Generator\PathResolver\FilterPathResolver;

class FilterGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummyFilters';
    }

    protected function getOutputFilePath(): PathResolverInterface
    {
        return new FilterPathResolver($this->generationConfiguration->getModel());
    }
}
