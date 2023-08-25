<?php

namespace Essa\APIToolKit\Generator\Commands;

class SeederGeneratorCommand extends GeneratorCommand
{
    protected string $type = 'seeder';

    protected function getStubName(): string
    {
        return 'DummySeeder';
    }
}
