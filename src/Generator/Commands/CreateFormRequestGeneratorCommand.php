<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Enum\GeneratorFilesType;
use Essa\APIToolKit\Generator\Contracts\HasDynamicContentInterface;
use Essa\APIToolKit\Generator\SchemaParsers\CreateValidationRulesParser;

class CreateFormRequestGeneratorCommand extends GeneratorCommand implements HasDynamicContentInterface
{
    protected GeneratorFilesType $type = GeneratorFilesType::CREATE_REQUEST;

    public function getContent(): array
    {
        return [
            '{{createValidationRules}}' => (new CreateValidationRulesParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }

    protected function getStubName(): string
    {
        return 'CreateDummyRequest';
    }
}
