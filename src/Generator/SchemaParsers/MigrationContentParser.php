<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\Contracts\SchemaParserInterface;
use Illuminate\Support\Str;

class MigrationContentParser implements SchemaParserInterface
{
    public function parse(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => $this->generateColumnDefinition($definition))
            ->implode(PHP_EOL);
    }

    private function generateColumnDefinition(string $definition): string
    {
        $parsedColumn = $this->parseColumnDefinition($definition);
        $columnName = $parsedColumn['columnName'];
        $columnType = $parsedColumn['columnType'];
        $options = $parsedColumn['options'];

        if ($this->isForeignKey($columnType)) {
            $columnDefinition = $this->getForeignKeyColumnDefinition($columnName);
        } else {
            $columnDefinition = "\$table->{$columnType}('{$columnName}')";
        }

        $optionsString = collect($options)
            ->map(fn ($option) => $this->addOption($option))
            ->implode('');

        return "\t\t\t" . $columnDefinition . $optionsString . ';';
    }

    private function getForeignKeyColumnDefinition(string $columnName): string
    {
        $relatedTable = Str::plural(Str::beforeLast($columnName, '_id'));

        return "\$table->foreignId('{$columnName}')->constrained('{$relatedTable}')";
    }

    private function addOption(string $option): string
    {
        return preg_match('/\(/', $option) ? "->{$option}" : "->{$option}()";
    }

    private function isForeignKey(string $columnType): bool
    {
        return 'foreignId' === $columnType;
    }


    private function parseColumnDefinition(string $definition): array
    {
        $parts = explode(':', $definition);
        $columnName = array_shift($parts);
        $columnType = count($parts) > 0 ? $parts[0] : 'string';
        $options = array_slice($parts, 1); // Rest of the parts are options

        return compact('columnName', 'columnType', 'options');
    }
}
