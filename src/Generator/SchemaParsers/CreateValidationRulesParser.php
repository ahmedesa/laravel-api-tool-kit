<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

class CreateValidationRulesParser extends SchemaParser
{
    protected function getParsedSchema(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => 'required',")
            ->implode(PHP_EOL . "\t\t\t");
    }
}
