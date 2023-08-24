<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\DTOs\ColumnDefinition;
use Essa\APIToolKit\Generator\DTOs\SchemaDefinition;

class FillableColumnsParser extends SchemaParser
{
    protected function getParsedSchema(SchemaDefinition $schemaDefinition): string
    {
        return collect($schemaDefinition->columns)
            ->map(fn (ColumnDefinition $definition): string => "'{$definition->name}',")
            ->implode(PHP_EOL . "\t\t");
    }
}
