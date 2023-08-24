<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\ResourceAttributesParser;

class ResourceGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'resourceContent' => (new ResourceAttributesParser($this->generationConfiguration->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'DummyResource';
    }

    protected function getOutputFolder(): string
    {
        return app_path("Http/Resources/{$this->generationConfiguration->getModel()}");
    }

    protected function getOutputFilePath(): string
    {
        return app_path("Http/Resources/{$this->generationConfiguration->getModel()}/{$this->generationConfiguration->getModel()}Resource.php");
    }
}
