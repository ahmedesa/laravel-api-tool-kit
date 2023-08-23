<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Essa\APIToolKit\Generator\SchemaParsers\ResourceAttributesParser;

class GeneratorResourceCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'DummyResource'; // Replace with the name of your resource stub
    }

    protected function getFolder(): string
    {
        return app_path("Http/Resources/{$this->model}");
    }

    protected function getFullPath(): string
    {
        return app_path("Http/Resources/{$this->model}/{$this->model}Resource.php");
    }

    protected function schemaReplacements(): array
    {
        $schemaParser = new ResourceAttributesParser();
        $output = $schemaParser->parse($this->schema);

        return [
            'resourceContent' => $output,
        ];
    }
}
