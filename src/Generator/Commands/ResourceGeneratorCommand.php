<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\HasDynamicContent;
use Essa\APIToolKit\Generator\SchemaParsers\ResourceAttributesParser;

class ResourceGeneratorCommand extends GeneratorCommand implements HasDynamicContent
{
    protected string $type = 'resource';

    public function getContent(): array
    {
        return [
            'resourceContent' => (new ResourceAttributesParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'DummyResource';
    }
}
