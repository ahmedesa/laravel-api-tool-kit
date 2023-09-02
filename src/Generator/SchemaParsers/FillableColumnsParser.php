<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\ColumnDefinition;
use Essa\APIToolKit\Generator\SchemaDefinition;

class FillableColumnsParser extends SchemaParser
{
    protected function getParsedSchema(SchemaDefinition $schemaDefinition): string
    {
        return collect($schemaDefinition->getColumns())
            ->map(fn (ColumnDefinition $definition): string => "'{$definition->getName()}',")
            ->implode(PHP_EOL . "\t\t");
    }
}
