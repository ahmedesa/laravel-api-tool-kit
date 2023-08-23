<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\Contracts\SchemaParserInterface;

class ResourceAttributesParser implements SchemaParserInterface
{
    public function parse(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => \$this->{$this->getColumnName($definition)},")
            ->implode(PHP_EOL . "\t\t\t");
    }

    private function getColumnName(string $definition): string
    {
        return explode(':', $definition)[0];
    }
}
