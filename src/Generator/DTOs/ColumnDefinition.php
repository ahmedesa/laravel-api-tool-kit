<?php

namespace Essa\APIToolKit\Generator\DTOs;

class ColumnDefinition
{
    public string $name;
    public string $type;
    public array $options;

    public function __construct(string $name, string $type, array $options)
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    public static function createFromDefinitionString($columnDefinitions): ColumnDefinition
    {
        [$name, $type, $options] = self::parseColumnDefinition($columnDefinitions);

        return new ColumnDefinition($name, $type, $options);
    }

    protected static function parseColumnDefinition(string $definition): array
    {
        $parts = explode(':', $definition);
        $name = array_shift($parts);
        $type = count($parts) > 0 ? $parts[0] : 'string';
        $options = array_slice($parts, 1);

        return [$name, $type, $options];
    }

    public function getOptionsAsString(): string
    {
        return implode(' , ', $this->options);
    }

    public function isForeignKey(): bool
    {
        return 'foreignId' === $this->type;
    }
}
