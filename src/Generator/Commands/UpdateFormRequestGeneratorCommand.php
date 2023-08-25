<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\UpdateValidationRulesParser;

class UpdateFormRequestGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    protected string $type = 'update-request';

    public function getSchemaReplacements(): array
    {
        return [
            'updateValidationRules' => (new UpdateValidationRulesParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'UpdateDummyRequest';
    }
}
