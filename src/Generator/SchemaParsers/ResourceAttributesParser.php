<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\Contracts\SchemaParserInterface;

class ResourceAttributesParser extends BaseSchemaParser implements SchemaParserInterface
{
    public function parse(): string
    {
        return collect($this->columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => \$this->{$this->getColumnName($definition)},")
            ->implode(PHP_EOL . "\t\t\t");
    }
}
