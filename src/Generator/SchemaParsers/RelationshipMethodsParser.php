<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Illuminate\Support\Str;

class RelationshipMethodsParser extends SchemaParser
{
    protected function getParsedSchema(array $columnDefinitions): string
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
}
