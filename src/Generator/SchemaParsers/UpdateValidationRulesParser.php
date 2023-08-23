<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\Contracts\SchemaParserInterface;

class UpdateValidationRulesParser extends BaseSchemaParser implements SchemaParserInterface
{
    public function parse(): string
    {
        return collect($this->columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => 'sometimes',")
            ->implode(PHP_EOL . "\t\t\t");
    }
}
