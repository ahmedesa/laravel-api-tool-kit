<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

class FactoryColumnsParser extends SchemaParser
{
    protected function getParsedSchema(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition) => $this->generateFactoryColumnDefinition($definition))
            ->implode(PHP_EOL . "\t\t\t");
    }

    private function generateFactoryColumnDefinition(string $definition): string
    {
        $columnName = $this->getColumnName($definition);
        $factoryMethod = $this->getFactoryMethod($this->getColumnType($definition));

        return "'{$columnName}' => \$this->faker->{$factoryMethod}(),";
    }

    private function getFactoryMethod(string $columnType): string
    {
        return match ($columnType) {
            'string', 'char' => 'firstName',
            'integer', 'unsignedInteger', 'bigInteger', 'unsignedBigInteger',
            'mediumInteger', 'tinyInteger', 'smallInteger' => 'randomNumber',
            'boolean' => 'boolean',
            'decimal', 'double', 'float' => 'randomFloat',
            'date', 'dateTime', 'dateTimeTz', 'timestamp', 'timestampTz','datetime' => 'dateTime',
            'time', 'timeTz' => 'time',
            'uuid' => 'uuid',
            'foreignId' => 'smallInteger',
            default => 'text',
        };
    }
}
