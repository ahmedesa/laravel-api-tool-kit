<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\CreateValidationRulesParser;

class CreateFormRequestGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'createValidationRules' => (new CreateValidationRulesParser($this->generationConfiguration->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'CreateDummyRequest';
    }

    protected function getOutputFolderPath(): string
    {
        return app_path("Http/Requests/{$this->generationConfiguration->getModel()}");
    }

    protected function getOutputFileName(): string
    {
        return "Create{$this->generationConfiguration->getModel()}Request.php";
    }
}
