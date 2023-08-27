<?php

namespace Essa\APIToolKit\Generator\Commands;

class ControllerGeneratorCommand extends GeneratorCommand
{
    protected string $type = 'controller';

    protected function getStubName(): string
    {
        return 'DummyController';
    }
}
