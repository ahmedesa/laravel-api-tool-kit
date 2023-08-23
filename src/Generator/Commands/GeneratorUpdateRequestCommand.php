<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\UpdateValidationRulesParser;

class GeneratorUpdateRequestCommand extends BaseGeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        $schemaParser = new UpdateValidationRulesParser();
        $output = $schemaParser->parse($this->schema);

        return [
            'updateValidationRules' => $output,
        ];
    }
    protected function getStubName(): string
    {
        return 'UpdateDummyRequest'; // Replace with the name of your update request stub
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
