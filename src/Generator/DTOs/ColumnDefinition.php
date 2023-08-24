<?php

namespace Essa\APIToolKit\Generator\DTOs;

class ColumnDefinition
{
    public function __construct(private string $name, private string $type, private array $options)
    {
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
