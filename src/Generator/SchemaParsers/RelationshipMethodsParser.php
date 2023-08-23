<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\Contracts\SchemaParserInterface;
use Illuminate\Support\Str;

class RelationshipMethodsParser implements SchemaParserInterface
{
    public function parse(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->filter(fn ($definition) => $this->isForeignKey($this->parseColumnDefinition($definition)['columnType']))
            ->map(fn ($definition) => $this->generateRelationshipMethod($definition))
            ->implode(PHP_EOL);
    }

    private function generateRelationshipMethod(string $definition): string
    {
        $parsedColumn = $this->parseColumnDefinition($definition);
        $columnName = $parsedColumn['columnName'];
        $relatedName = Str::camel(Str::beforeLast($columnName, '_id'));
        $relatedModel = Str::studly(Str::beforeLast($columnName, '_id'));

        return "\tpublic function {$relatedName}(): \Illuminate\Database\Eloquent\Relations\BelongsTo\n\t{\n\t\treturn \$this->belongsTo(\App\Models\\{$relatedModel}::class);\n\t}\n";
    }

    private function isForeignKey(string $columnType): bool
    {
        return 'foreignId' === $columnType;
    }


    private function parseColumnDefinition(string $definition): array
    {
        $parts = explode(':', $definition);
        $columnName = array_shift($parts);
        $columnType = count($parts) > 0 ? $parts[0] : 'string';
        $options = array_slice($parts, 1); // Rest of the parts are options

        return compact('columnName', 'columnType', 'options');
    }
}
