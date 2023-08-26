<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\DTOs\ColumnDefinition;
use Essa\APIToolKit\Generator\DTOs\SchemaDefinition;

class CreateValidationRulesParser extends SchemaParser
{
    protected function getParsedSchema(SchemaDefinition $schemaDefinition): string
    {
        return collect($schemaDefinition->getColumns())
            ->map(fn (ColumnDefinition $definition): string => "'{$definition->getName()}' => [{$this->guessValidationRule($definition, ['required'])}],")
            ->implode(PHP_EOL . "\t\t\t");
    }


    protected function guessValidationRule(ColumnDefinition $definition, array $extraValidation): string
    {
        $rules = $extraValidation + [];

        if (str_contains($definition->getName(), 'email')) {

            $rules[] = 'email';
        }

        if (str_contains($definition->getName(), 'image')) {
            $rules[] = 'image';
        }

        if (in_array($definition->getType(), ['string', 'text'])) {
            $rules[] = 'string';
        }

        if (in_array($definition->getType(), ['integer', 'bigInteger', 'unsignedBigInteger', 'mediumInteger', 'tinyInteger', 'smallInteger'])) {
            $rules[] = 'integer';
        }

        if ('boolean' === $definition->getType()) {
            $rules[] = 'boolean';
        }

        if (in_array($definition->getType(), ['decimal', 'double', 'float'])) {
            $rules[] = 'numeric';
        }

        if (in_array($definition->getType(), ['date', 'dateTime', 'dateTimeTz', 'timestamp', 'timestampTz'])) {
            $rules[] = 'date';
        }

        if (in_array($definition->getType(), ['time', 'timeTz'])) {
            $rules[] = 'date_format:H:i:s';
        }

        if (in_array($definition->getType(), ['uuid', 'uuidBinary'])) {
            $rules[] = 'uuid';
        }

        if ('ipAddress' === $definition->getType()) {
            $rules[] = 'ip';
        }

        if (in_array($definition->getType(), ['json', 'jsonb'])) {
            $rules[] = 'json';
        }

        return "'" . implode("', '", $rules) . "'";
    }
}
