<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Enum\GeneratorFilesType;

class FilterGeneratorCommand extends GeneratorCommand
{
    protected string $type = GeneratorFilesType::FILTER;

    protected function getStubName(): string
    {
        return 'DummyFilters';
    }
}
