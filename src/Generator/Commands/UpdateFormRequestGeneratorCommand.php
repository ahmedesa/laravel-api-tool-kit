<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\UpdateValidationRulesParser;

class UpdateFormRequestGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'updateValidationRules' => (new UpdateValidationRulesParser($this->schema))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'UpdateDummyRequest';
    }

    protected function getOutputFolder(): string
    {
        return app_path("Http/Requests/{$this->model}");
    }

    protected function getOutputFilePath(): string
    {
        return app_path("Http/Requests/{$this->model}/Update{$this->model}Request.php");
    }
}
