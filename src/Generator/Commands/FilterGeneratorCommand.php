<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\HasDynamicContent;

class FilterGeneratorCommand extends GeneratorCommand implements HasDynamicContent
{
    protected string $type = 'filter';

    public function getContent(): array
    {
        return [
            '{{DummyFilters}}' => "{$this->apiGenerationCommandInputs->getModel()}Filters",
        ];
    }

    protected function getStubName(): string
    {
        return 'DummyFilters';
    }
}
