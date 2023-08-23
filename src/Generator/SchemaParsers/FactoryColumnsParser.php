<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\Contracts\SchemaParserInterface;

class FactoryColumnsParser implements SchemaParserInterface
{
    public function parse(array $columnDefinitions): string
    {
        return collect($columnDefinitions)
            ->map(fn ($definition): string => "'{$this->getColumnName($definition)}' => \$this->faker->{$this->getFactoryMethod($this->getColumnType($definition))},")
            ->implode(PHP_EOL . "\t\t\t");
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
}
