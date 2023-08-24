<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\DTOs\ColumnDefinition;
use Essa\APIToolKit\Generator\DTOs\SchemaDefinition;
use Illuminate\Support\Str;

class MigrationContentParser extends SchemaParser
{
    protected function getParsedSchema(SchemaDefinition $schemaDefinition): string
    {
        return collect($schemaDefinition->columns)
            ->map(fn (ColumnDefinition $definition): string => $this->generateColumnDefinition($definition))
            ->implode(PHP_EOL);
    }

    private function generateColumnDefinition(ColumnDefinition $definition): string
    {
        $columnDefinition = $this->getColumnDefinition($definition->name, $definition->type);
        $optionsString = $this->getOptionString($definition->options);

        return "\t\t\t" . $columnDefinition . $optionsString . ';';
    }

    private function getColumnDefinition(string $columnName, string $columnType): string
    {
        if ($this->isForeignKey($columnType)) {
            return $this->getForeignKeyColumnDefinition($columnName);
        }

        return "\$table->{$columnType}('{$columnName}')";
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
}
