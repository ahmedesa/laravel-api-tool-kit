<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Enum\GeneratorFilesType;

class TestGeneratorCommand extends GeneratorCommand
{
    protected string $type = GeneratorFilesType::TEST;

    protected function getStubName(): string
    {
        return 'DummyTest';
    }
}
