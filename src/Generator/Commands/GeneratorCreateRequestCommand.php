<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Essa\APIToolKit\Generator\SchemaParsers\CreateValidationRulesParser;

class GeneratorCreateRequestCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'CreateDummyRequest'; // Replace with the name of your request stub
    }

    protected function getFolder(): string
    {
        return app_path("Http/Requests/{$this->model}");
    }

    protected function getFullPath(): string
    {
        return app_path("Http/Requests/{$this->model}/Create{$this->model}Request.php");
    }

    protected function schemaReplacements(): array
    {
        $schemaParser = new CreateValidationRulesParser();
        $output = $schemaParser->parse($this->schema);

        return [
            'createValidationRules' => $output,
        ];
    }
}
