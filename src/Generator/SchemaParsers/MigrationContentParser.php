<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\Contracts\SchemaParserInterface;
use Illuminate\Support\Str;

class MigrationContentParser extends BaseSchemaParser implements SchemaParserInterface
{
    public function parse(): string
    {
        return collect($this->columnDefinitions)
            ->map(fn ($definition) => $this->generateColumnDefinition($definition))
            ->implode(PHP_EOL);
    }

    private function generateColumnDefinition(string $definition): string
    {
        $parsedColumn = $this->parseColumnDefinition($definition);
        $columnName = $parsedColumn['columnName'];
        $columnType = $parsedColumn['columnType'];
        $options = $parsedColumn['options'];

        $columnDefinition = $this->getColumnDefinition($columnName, $columnType);
        $optionsString = $this->getOptionString($options);

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
