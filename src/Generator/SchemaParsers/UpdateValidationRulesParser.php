<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\ColumnDefinition;
use Essa\APIToolKit\Generator\Guessers\ValidationRuleGuesser;
use Essa\APIToolKit\Generator\SchemaDefinition;

class UpdateValidationRulesParser extends SchemaParser
{
    protected function getParsedSchema(SchemaDefinition $schemaDefinition): string
    {
        return collect($schemaDefinition->getColumns())
            ->map(function (ColumnDefinition $definition): string {
                $guesser = new ValidationRuleGuesser($definition, ['sometimes']);

                return "'{$definition->getName()}' => [{$guesser->guess()}],";
            })
            ->implode(PHP_EOL . "\t\t\t");
    }
}
