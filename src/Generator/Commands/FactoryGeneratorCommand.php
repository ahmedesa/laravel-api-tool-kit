<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\FactoryColumnsParser;

class FactoryGeneratorCommand extends BaseGeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'factoryContent' => (new FactoryColumnsParser($this->schema))->parse(),
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
        return database_path("factories/{$this->model}Factory.php");
    }
}
