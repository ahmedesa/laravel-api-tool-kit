<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Enum\GeneratorFilesType;

class ControllerGeneratorCommand extends GeneratorCommand
{
    protected string $type = GeneratorFilesType::CONTROLLER;

    protected function getStubName(): string
    {
        return 'DummyController';
    }
}
