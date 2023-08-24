<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\PathResolver\FactoryPathResolver;
use Essa\APIToolKit\Generator\SchemaParsers\FactoryColumnsParser;

class FactoryGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'factoryContent' => (new FactoryColumnsParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'DummyFactory';
    }

    protected function getOutputFilePath(): PathResolverInterface
    {
        return new FactoryPathResolver($this->apiGenerationCommandInputs->getModel());
    }
}
