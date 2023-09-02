<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\ColumnDefinition;
use Essa\APIToolKit\Generator\SchemaDefinition;

class ResourceAttributesParser extends SchemaParser
{
    protected function getParsedSchema(SchemaDefinition $schemaDefinition): string
    {
        return collect($schemaDefinition->getColumns())
            ->map(fn (ColumnDefinition $definition): string => "'{$definition->getName()}' => {$this->value($definition)},")
            ->implode(PHP_EOL . "\t\t\t");
    }

    private function value(ColumnDefinition $definition): string
    {
        $value = "\$this->{$definition->getName()}";

        if ($definition->isTimeType()) {
            return "dateTimeFormat({$value})";
        }

        return $value;
    }
}
