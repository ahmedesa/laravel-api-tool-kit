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
            factoryContent: $this->generateFactoryColumns($columnDefinitions)
        );
    }

    private function generateFillableColumns(array $columnDefinitions): string
    {
        $fillableColumns = '';

        foreach ($columnDefinitions as $definition) {
            list($columnName) = explode(':', $definition);
            $fillableColumns .= "'{$columnName}'," . PHP_EOL;
        }

        return $fillableColumns;
    }

    private function generateFactoryColumns(array $columnDefinitions): string
    {
        $factoryColumns = '';

        foreach ($columnDefinitions as $definition) {
            list($columnName, $columnType) = explode(':', $definition);
            $factoryColumns .= "'{$columnName}' => \$this->{$this->getFactoryMethod($columnType)}," . PHP_EOL;
        }

        return $factoryColumns;
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

    private function generateResourceAttributes(array $columnDefinitions): string
    {
        $attributes = '';

        foreach ($columnDefinitions as $definition) {
            list($columnName) = explode(':', $definition);
            $attributes .= "\n\t\t\t'{$columnName}' => \$this->{$columnName},";
        }

        return $attributes;
    }

    private function generateMigrationContent(array $columnDefinitions): string
    {
        $migrationContent = '';

        foreach ($columnDefinitions as $definition) {
            list($columnName, $columnType) = explode(':', $definition);
            $migrationContent .= "\t\t\t" . "\$table->{$columnType}('{$columnName}');" . PHP_EOL;

            if ($this->isForeignKey($columnType)) {
                $migrationContent .= $this->generateForeignKey($columnName);
            }
        }

        return $migrationContent;
    }

    private function isForeignKey(string $columnType): bool
    {
        return 'foreignId' === $columnType;
    }

    private function generateForeignKey(string $columnName): string
    {
        $relatedTable = Str::plural(Str::beforeLast($columnName, '_id'));

        return "\t\t\t\$table->foreignId('{$columnName}')->constrained('{$relatedTable}')->cascadeOnDelete();" . PHP_EOL;
    }
}
