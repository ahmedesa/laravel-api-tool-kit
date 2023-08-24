<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\FactoryColumnsParser;

class FactoryGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'factoryContent' => (new FactoryColumnsParser($this->generationConfiguration->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'DummyFactory';
    }

    protected function getOutputFolder(): string
    {
        return database_path('/factories');
    }

    protected function getOutputFilePath(): string
    {
        return database_path("factories/{$this->generationConfiguration->getModel()}Factory.php");
    }
}
