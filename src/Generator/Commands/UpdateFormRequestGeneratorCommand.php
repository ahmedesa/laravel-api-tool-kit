<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\UpdateValidationRulesParser;

class UpdateFormRequestGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'updateValidationRules' => (new UpdateValidationRulesParser($this->generationConfiguration->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'UpdateDummyRequest';
    }

    protected function getOutputFolder(): string
    {
        return app_path("Http/Requests/{$this->generationConfiguration->getModel()}");
    }

    protected function getOutputFilePath(): string
    {
        return app_path("Http/Requests/{$this->generationConfiguration->getModel()}/Update{$this->generationConfiguration->getModel()}Request.php");
    }
}
