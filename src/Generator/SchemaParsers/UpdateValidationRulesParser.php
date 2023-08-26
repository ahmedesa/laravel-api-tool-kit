<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\DTOs\ColumnDefinition;
use Essa\APIToolKit\Generator\DTOs\SchemaDefinition;

class UpdateValidationRulesParser extends CreateValidationRulesParser
{
    protected function getParsedSchema(SchemaDefinition $schemaDefinition): string
    {
        return collect($schemaDefinition->getColumns())
            ->map(fn (ColumnDefinition $definition): string => "'{$definition->getName()}' => [{$this->guessValidationRule($definition, ['sometimes'])}],")
            ->implode(PHP_EOL . "\t\t\t");
    }
}
