<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\ResourceAttributesParser;

class GeneratorResourceCommand extends BaseGeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        $schemaParser = new ResourceAttributesParser();
        $output = $schemaParser->parse($this->schema);

        return [
            'resourceContent' => $output,
        ];
    }
    protected function getStubName(): string
    {
        return 'DummyResource';
    }

    protected function getOutputFolder(): string
    {
        return app_path("Http/Resources/{$this->model}");
    }

    protected function getOutputFilePath(): string
    {
        return app_path("Http/Resources/{$this->model}/{$this->model}Resource.php");
    }
}
