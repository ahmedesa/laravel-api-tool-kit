<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\CreateValidationRulesParser;

class CreateFormRequestGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'createValidationRules' => (new CreateValidationRulesParser($this->schema))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'CreateDummyRequest';
    }

    protected function getOutputFolder(): string
    {
        return app_path("Http/Requests/{$this->model}");
    }

    protected function getOutputFilePath(): string
    {
        return app_path("Http/Requests/{$this->model}/Create{$this->model}Request.php");
    }
}
