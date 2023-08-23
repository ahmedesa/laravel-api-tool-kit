<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\Contracts\SchemaParserInterface;

class CreateValidationRulesParser extends BaseSchemaParser implements SchemaParserInterface
{
    public function parse(): string
    {
        return collect($this->columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => 'required',")
            ->implode(PHP_EOL . "\t\t\t");
    }
}
