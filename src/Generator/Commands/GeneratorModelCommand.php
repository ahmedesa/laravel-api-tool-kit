<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\FillableColumnsParser;
use Essa\APIToolKit\Generator\SchemaParsers\RelationshipMethodsParser;

class GeneratorModelCommand extends BaseGeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        $schemaParser = new FillableColumnsParser();
        $output1 = $schemaParser->parse($this->schema);

        $schemaParser = new RelationshipMethodsParser();
        $output = $schemaParser->parse($this->schema);

        return [
            'fillableColumns' => $output1,
            'modelRelations' => $output,
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
