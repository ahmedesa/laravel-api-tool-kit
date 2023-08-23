<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\Contracts\SchemaParserInterface;

class CreateValidationRulesParser implements SchemaParserInterface
{
    public function parse(array $columnDefinitions): string
    {

        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => 'required',")
            ->implode(PHP_EOL . "\t\t\t");
    }


    private function getColumnName(string $definition): string
    {
        return explode(':', $definition)[0];
    }
}
