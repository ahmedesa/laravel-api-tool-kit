<?php

namespace Essa\APIToolKit\Generator\DTOs;

class SchemaDefinition
{
    public function __construct(private array $columns)
    {
    }

    public static function createFromSchemaString(?string $schema): SchemaDefinition
    {
        if ( ! $schema) {
            return new self([]);
        }

        $columnDefinitions = explode(',', $schema);

        $columns = [];

        foreach ($columnDefinitions as $columnDefinition) {
            $columns[] = ColumnDefinition::createFromDefinitionString($columnDefinition);
        }

        return new self($columns);
    }

    public function getColumns(): array
    {
        return $this->columns;
    }
}
