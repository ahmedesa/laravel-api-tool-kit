<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Essa\APIToolKit\Generator\PathResolver\ControllerPathResolver;

class ControllerGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummyController';
    }
    protected function getOutputFilePath(): PathResolverInterface
    {
        return new ControllerPathResolver($this->generationConfiguration->getModel());
    }
}
