<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\ResourceAttributesParser;

class ResourceGeneratorCommand extends BaseGeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'resourceContent' => (new ResourceAttributesParser($this->schema))->parse(),
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
