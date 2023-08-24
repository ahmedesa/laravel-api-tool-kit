<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\DTOs\ColumnDefinition;
use Essa\APIToolKit\Generator\DTOs\SchemaDefinition;
use Illuminate\Support\Str;

class RelationshipMethodsParser extends SchemaParser
{
    protected function getParsedSchema(SchemaDefinition $schemaDefinition): string
    {
        return collect($schemaDefinition->columns)
            ->filter(fn (ColumnDefinition $definition): bool => $this->isForeignKey($definition->type))
            ->map(fn (ColumnDefinition $definition): string => $this->generateRelationshipMethod($definition))
            ->implode(PHP_EOL);
    }

    private function generateRelationshipMethod(ColumnDefinition $definition): string
    {
        $columnName = $definition->name;
        $relatedName = Str::camel(Str::beforeLast($columnName, '_id'));
        $relatedModel = Str::studly(Str::beforeLast($columnName, '_id'));

        return "\tpublic function {$relatedName}(): \Illuminate\Database\Eloquent\Relations\BelongsTo\n\t{\n\t\treturn \$this->belongsTo(\App\Models\\{$relatedModel}::class);\n\t}\n";
    }
}
