<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

class ResourceAttributesParser extends SchemaParser
{
    protected function getParsedSchema(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => \$this->{$this->getColumnName($definition)},")
            ->implode(PHP_EOL . "\t\t\t");
    }
}
