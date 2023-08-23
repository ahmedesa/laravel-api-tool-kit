<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

class UpdateValidationRulesParser extends SchemaParser
{
    protected function getParsedSchema(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => 'sometimes',")
            ->implode(PHP_EOL . "\t\t\t");
    }
}
