<?php

namespace Essa\APIToolKit\Generator\Commands;

class FilterGeneratorCommand extends GeneratorCommand
{
    protected string $type = 'filter';

    protected function getStubName(): string
    {
        return 'DummyFilters';
    }
}
