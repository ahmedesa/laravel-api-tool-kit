<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Essa\APIToolKit\Generator\SchemaParsers\FillableColumnsParser;
use Essa\APIToolKit\Generator\SchemaParsers\RelationshipMethodsParser;

class GeneratorModelCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'Dummy';
    }

    protected function getFolder(): string
    {
        return app_path('/Models');
    }

    protected function getFullPath(): string
    {
        return app_path("Models/{$this->model}.php");
    }

    protected function schemaReplacements(): array
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
}
