<?php

namespace Essa\APIToolKit\Generator\Commands;

class TestGeneratorCommand extends GeneratorCommand
{
    protected string $type = 'test';

    protected function getStubName(): string
    {
        return 'DummyTest';
    }
}
