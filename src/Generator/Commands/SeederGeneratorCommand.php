<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Enum\GeneratorFilesType;

class SeederGeneratorCommand extends GeneratorCommand
{
    protected string $type = GeneratorFilesType::SEEDER;

    protected function getStubName(): string
    {
        return 'DummySeeder';
    }
}
