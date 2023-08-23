<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\FillableColumnsParser;
use Essa\APIToolKit\Generator\SchemaParsers\RelationshipMethodsParser;

class ModelGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'fillableColumns' =>  (new FillableColumnsParser($this->schema))->parse(),
            'modelRelations' => (new RelationshipMethodsParser($this->schema))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'Dummy';
    }

    protected function getOutputFolder(): string
    {
        return app_path('/Models');
    }

    protected function getOutputFilePath(): string
    {
        return app_path("Models/{$this->model}.php");
    }
}
