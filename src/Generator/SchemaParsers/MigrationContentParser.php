<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\ColumnDefinition;
use Essa\APIToolKit\Generator\SchemaDefinition;
use Illuminate\Support\Str;

class MigrationContentParser extends SchemaParser
{
    protected function getParsedSchema(SchemaDefinition $schemaDefinition): string
    {
        return collect($schemaDefinition->getColumns())
            ->map(fn (ColumnDefinition $definition): string => $this->generateColumnDefinition($definition))
            ->implode(PHP_EOL . "\t\t\t");
    }

    private function generateColumnDefinition(ColumnDefinition $definition): string
    {
        $columnDefinition = $this->getColumnDefinition($definition);
        $optionsString = $this->getOptionString($definition->getOptions());

        return $columnDefinition . $optionsString . ';';
    }

    private function getColumnDefinition(ColumnDefinition $definition): string
    {
        if ($definition->isForeignKey()) {
            return $this->getForeignKeyColumnDefinition($definition->getName());
        }

        if ($definition->isEnum()) {
            return $this->getEnumColumnDefinition($definition);
        }

        return "\$table->{$definition->getType()}('{$definition->getName()}')";
    }

    private function getForeignKeyColumnDefinition(string $columnName): string
    {
        $relatedTable = Str::plural(Str::beforeLast($columnName, '_id'));

        return "\$table->foreignId('{$columnName}')->constrained('{$relatedTable}')";
    }

    private function getOptionString(array $options): string
    {
        return collect($options)
            ->map(fn ($option) => $this->addOption($option))
            ->implode('');
    }

    private function addOption(string $option): string
    {
        return preg_match('/\(/', $option) ? "->{$option}" : "->{$option}()";
    }

    private function getEnumColumnDefinition(ColumnDefinition $definition): string
    {
        $enumValuesString = "'" . implode("', '", $definition->getEnumValues()) . "'";

        return "\$table->enum('{$definition->getName()}', [{$enumValuesString}])";
    }
}
