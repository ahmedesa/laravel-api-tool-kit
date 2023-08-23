<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\CreateValidationRulesParser;

class GeneratorCreateRequestCommand extends BaseGeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        $schemaParser = new CreateValidationRulesParser();
        $output = $schemaParser->parse($this->schema);

        return [
            'createValidationRules' => $output,
        ];
    }
    protected function getStubName(): string
    {
        return 'CreateDummyRequest'; // Replace with the name of your request stub
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
