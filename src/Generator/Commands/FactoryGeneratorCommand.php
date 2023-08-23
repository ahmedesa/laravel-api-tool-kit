<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\FactoryColumnsParser;

class FactoryGeneratorCommand extends BaseGeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        $schemaParser = new FactoryColumnsParser();
        $output = $schemaParser->parse($this->schema);

        return [
            'factoryContent' => $output,
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
