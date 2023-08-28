<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\HasDynamicContent;
use Essa\APIToolKit\Generator\SchemaParsers\CreateValidationRulesParser;

class CreateFormRequestGeneratorCommand extends GeneratorCommand implements HasDynamicContent
{
    protected string $type = 'create-request';

    public function getContent(): array
    {
        return [
            'createValidationRules' => (new CreateValidationRulesParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }

    protected function getStubName(): string
    {
        return 'CreateDummyRequest';
    }
}
