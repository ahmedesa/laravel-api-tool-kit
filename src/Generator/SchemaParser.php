<?php

namespace Essa\APIToolKit\Generator;

use Essa\APIToolKit\Generator\DTOs\SchemaParserOutput;
use Illuminate\Support\Str;

class SchemaParser
{
    public function __construct(private ?string $schema)
    {
    }

    public function parse(): SchemaParserOutput
    {
        if ( ! $this->schema) {
            return new SchemaParserOutput();
        }

        $columnDefinitions = explode(',', $this->schema);

        return new SchemaParserOutput(
            fillableColumns: $this->generateFillableColumns($columnDefinitions),
            migrationContent: $this->generateMigrationContent($columnDefinitions),
            resourceContent: $this->generateResourceAttributes($columnDefinitions),
            factoryContent: $this->generateFactoryColumns($columnDefinitions),
            createValidationRules: $this->generateValidationRules($columnDefinitions),
            updateValidationRules: $this->generateValidationRules($columnDefinitions, true),
            modelRelations: $this->generateRelationshipMethods($columnDefinitions, true),
        );
    }


    private function generateFillableColumns(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}',")
            ->implode(PHP_EOL . "\t\t");
    }

    private function generateFactoryColumns(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => \$this->faker->{$this->getFactoryMethod($this->getColumnType($definition))},")
            ->implode(PHP_EOL . "\t\t\t");
    }

    private function generateResourceAttributes(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => \$this->{$this->getColumnName($definition)},")
            ->implode(PHP_EOL . "\t\t\t");
    }

    private function generateValidationRules($columnDefinitions, $isUpdateRequest = false): string
    {
        $ruleType = $isUpdateRequest ? 'sometimes' : 'required';

        return collect($columnDefinitions)
            ->map(fn ($definition) => "'{$this->getColumnName($definition)}' => '{$ruleType}',")
            ->implode(PHP_EOL . "\t\t\t");
    }

    private function generateRelationshipMethods(array $columnDefinitions): string
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

    private function generateMigrationContent(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => $this->generateColumnDefinition($definition))
            ->implode(PHP_EOL);
    }

    private function generateColumnDefinition(string $definition): string
    {
        $parsedColumn = $this->parseColumnDefinition($definition);
        $columnName = $parsedColumn['columnName'];
        $columnType = $parsedColumn['columnType'];
        $options = $parsedColumn['options'];

        if ($this->isForeignKey($columnType)) {
            $columnDefinition = $this->getForeignKeyColumnDefinition($columnName);
        } else {
            $columnDefinition = "\$table->{$columnType}('{$columnName}')";
        }

        $optionsString = collect($options)
            ->map(fn ($option) => $this->addOption($option))
            ->implode('');

        return "\t\t\t" . $columnDefinition . $optionsString . ';';
    }

    private function getForeignKeyColumnDefinition(string $columnName): string
    {
        $relatedTable = Str::plural(Str::beforeLast($columnName, '_id'));

        return "\$table->foreignId('{$columnName}')->constrained('{$relatedTable}')";
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

    private function getColumnName(string $definition): string
    {
        return explode(':', $definition)[0];
    }

    private function getColumnType(string $definition): string
    {
        return explode(':', $definition)[1];
    }

    private function getFactoryMethod(string $columnType): string
    {
        return match ($columnType) {
            'string', 'char' => 'firstName',
            'integer', 'unsignedInteger', 'bigInteger', 'unsignedBigInteger',
            'mediumInteger', 'tinyInteger', 'smallInteger' => 'randomNumber',
            'boolean' => 'boolean',
            'decimal', 'double', 'float' => 'randomFloat',
            'date', 'dateTime', 'dateTimeTz', 'timestamp', 'timestampTz' => 'dateTime',
            'time', 'timeTz' => 'time',
            'uuid' => 'uuid',
            'foreignId' => 'smallInteger',
            default => 'text',
        };
    }

    private function addOption(string $option): string
    {
        return preg_match('/\(/', $option) ? "->{$option}" : "->{$option}()";
    }
}
