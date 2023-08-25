<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\ResourceAttributesParser;

class ResourceGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    protected string $type = 'resource';

    public function getSchemaReplacements(): array
    {
        return [
            'resourceContent' => (new ResourceAttributesParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'DummyResource';
    }
}
