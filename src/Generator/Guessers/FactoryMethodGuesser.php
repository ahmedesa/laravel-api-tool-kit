<?php

namespace Essa\APIToolKit\Generator\Guessers;

use Essa\APIToolKit\Generator\Contracts\Guesser;
use Essa\APIToolKit\Generator\DTOs\ColumnDefinition;

class FactoryMethodGuesser implements Guesser
{
    public function __construct(private ColumnDefinition $definition)
    {
    }

    public function guess(): string
    {
        if ($this->isEmailColumn($this->definition)) {
            return 'safeEmail()';
        }

        if ($this->definition->isEnum()) {
            $enumValuesString = "'" . implode("', '", $this->definition->getEnumValues()) . "'";

            return "randomElement([{$enumValuesString}])";
        }

        return $this->guessByType() . "()";
    }

    private function isEmailColumn(ColumnDefinition $definition): bool
    {
        return str_contains($definition->getName(), 'email');
    }

    private function guessByType(): string
    {
        return match ($this->definition->getType()) {
            'string', 'char' => 'firstName',
            'integer', 'unsignedInteger', 'bigInteger', 'unsignedBigInteger',
            'mediumInteger', 'tinyInteger', 'smallInteger', 'foreignId' => 'randomNumber',
            'boolean' => 'boolean',
            'decimal', 'double', 'float' => 'randomFloat',
            'date', 'dateTime', 'dateTimeTz', 'timestamp', 'timestampTz', 'datetime' => 'dateTime',
            'time', 'timeTz' => 'time',
            'uuid' => 'uuid',
            default => 'text',
        };
    }
}
