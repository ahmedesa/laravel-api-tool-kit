<?php

namespace Essa\APIToolKit\Generator;

use Essa\APIToolKit\Generator\Exception\SchemaNotValidException;

class SchemaDefinition
{
    public function __construct(private array $columns)
    {
    }

    /**
     * Create a SchemaDefinition object from a schema string.
     *
     * @param string|null $schema The schema string to parse.
     *
     * @return SchemaDefinition Returns a SchemaDefinition object.
     * @throws SchemaNotValidException
     */
    public static function createFromSchemaString(?string $schema): SchemaDefinition
    {
        if ( ! $schema) {
            return new self([]);
        }

        self::validateSchema($schema);

        $columnDefinitions = explode('|', $schema);

        $columns = [];

        foreach ($columnDefinitions as $columnDefinition) {
            $columns[] = ColumnDefinition::createFromDefinitionString($columnDefinition);
        }

        return new self($columns);
    }

    /**
     * Validates a schema string of the format "COLUMN_NAME:COLUMN_TYPE:OPTIONS|..."
     *
     * COLUMN_NAME: Alphanumeric characters, underscores, or numbers.
     * COLUMN_TYPE: Any characters, parentheses, commas, underscores, or numbers.
     * OPTIONS: Optional, can be any characters, parentheses, commas, underscores, single quotes, or numbers.
     *
     * @param string|null $schema
     * @return void throw Exception if schema not valid.
     * @throws SchemaNotValidException
     */
    private static function validateSchema(?string $schema): void
    {
        $pattern = '/^([0-9a-zA-Z_]+:[0-9a-zA-Z(),_]+(:[0-9a-zA-Z(),_\'"]+)?)((\|[0-9a-zA-Z_]+:[0-9a-zA-Z(),_]+(:[0-9a-zA-Z(),_\'"]+)?)*)$/';

        if ( ! preg_match($pattern, $schema)) {
            throw new SchemaNotValidException();
        }
    }

    /**
     * Get the array of ColumnDefinition objects.
     *
     * @return array Returns an array of ColumnDefinition objects.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
