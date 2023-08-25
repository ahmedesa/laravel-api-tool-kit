<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\FactoryColumnsParser;

class FactoryGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    protected string $type = 'factory';

    public function getSchemaReplacements(): array
    {
        return [
            '{factoryContent}' => (new FactoryColumnsParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'DummyFactory';
    }
}
