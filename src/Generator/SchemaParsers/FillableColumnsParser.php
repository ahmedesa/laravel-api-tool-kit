<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

class FillableColumnsParser extends SchemaParser
{
    protected function getParsedSchema(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}',")
            ->implode(PHP_EOL . "\t\t");
    }
}
