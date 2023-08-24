<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\DTOs\SchemaDefinition;

abstract class SchemaParser
{
    public function __construct(private SchemaDefinition $schema)
    {
    }

    public function parse(): string
    {
        if (empty($this->schema->columns)) {
            return '';
        }

        return $this->getParsedSchema($this->schema);
    }

    abstract protected function getParsedSchema(SchemaDefinition $schemaDefinition): string;
}
