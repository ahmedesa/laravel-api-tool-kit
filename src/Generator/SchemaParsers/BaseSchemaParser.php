<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

class BaseSchemaParser
{
    public function __construct(protected array $columnDefinitions)
    {
    }

    protected function getColumnName(string $definition): string
    {
        return explode(':', $definition)[0];
    }

    protected function getColumnType(string $definition): string
    {
        return explode(':', $definition)[1];
    }

    protected function isForeignKey(string $columnType): bool
    {
        return 'foreignId' === $columnType;
    }

    protected function parseColumnDefinition(string $definition): array
    {
        $parts = explode(':', $definition);
        $columnName = array_shift($parts);
        $columnType = count($parts) > 0 ? $parts[0] : 'string';
        $options = array_slice($parts, 1); // Rest of the parts are options

        return compact('columnName', 'columnType', 'options');
    }
}
