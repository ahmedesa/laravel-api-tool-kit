<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\HasDynamicContent;
use Essa\APIToolKit\Generator\SchemaParsers\FactoryColumnsParser;

class FactoryGeneratorCommand extends GeneratorCommand implements HasDynamicContent
{
    protected string $type = 'factory';

    public function getContent(): array
    {
        return [
            '{{factoryContent}}' => (new FactoryColumnsParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'DummyFactory';
    }
}
