<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\Contracts\SchemaParserInterface;

class UpdateValidationRulesParser implements SchemaParserInterface
{
    public function parse(array $columnDefinitions): string
    {

        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => 'sometimes',")
            ->implode(PHP_EOL . "\t\t\t");
    }


    private function getColumnName(string $definition): string
    {
        return explode(':', $definition)[0];
    }
}
