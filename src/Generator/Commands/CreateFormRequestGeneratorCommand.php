<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\PathResolver\CreateFormRequestPathResolver;
use Essa\APIToolKit\Generator\SchemaParsers\CreateValidationRulesParser;

class CreateFormRequestGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'createValidationRules' => (new CreateValidationRulesParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'CreateDummyRequest';
    }

    protected function getOutputFolderPath(): string
    {
        return app_path("Http/Requests/{$this->apiGenerationCommandInputs->getModel()}");
    }

    protected function getOutputFilePath(): PathResolverInterface
    {
        return new CreateFormRequestPathResolver($this->apiGenerationCommandInputs->getModel());
    }
}
