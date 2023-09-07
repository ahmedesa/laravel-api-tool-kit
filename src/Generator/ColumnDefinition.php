<?php

namespace Essa\APIToolKit\Generator;

class ColumnDefinition
{
    public function __construct(private string $name, private string $type, private array $options)
    {
    }

    public static function createFromDefinitionString(string $columnDefinitions): ColumnDefinition
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        if ($this->isEnumType()) {
            return 'enum';
        }

        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOptionsAsString(): string
    {
        return implode(' , ', $this->options);
    }

    public function isForeignKey(): bool
    {
        return 'foreignId' === $this->type;
    }

    public function isEnumType(): bool
    {
        return str_contains($this->type, 'enum(');
    }

    public function getEnumValues(): array
    {
        return array_map('trim', explode(',', trim($this->type, 'enum() ')));
    }

    public function isEmailType(): bool
    {
        return str_contains($this->name, 'email');
    }

    public function isImageType(): bool
    {
        return str_contains($this->name, 'image');
    }

    public function isBooleanType(): bool
    {
        return 'boolean' === $this->type;
    }

    public function isUuidType(): bool
    {
        return in_array($this->type, ['uuid', 'uuidBinary']);
    }

    public function isIpAddressType(): bool
    {
        return 'ipAddress' === $this->type;
    }

    public function isJsonType(): bool
    {
        return in_array($this->type, ['json', 'jsonb']);
    }

    public function isIntegerType(): bool
    {
        return in_array($this->type, ['integer', 'bigInteger', 'unsignedBigInteger', 'mediumInteger', 'tinyInteger', 'smallInteger']);
    }

    public function isNumericType(): bool
    {
        return in_array($this->type, ['decimal', 'double', 'float']);
    }

    public function isStringOrTextType(): bool
    {
        return in_array($this->type, ['string', 'text']) && ! $this->isImageType();
    }

    public function isDateType(): bool
    {
        return in_array($this->type, ['date', 'dateTime', 'dateTimeTz', 'timestamp', 'timestampTz' , 'datetime']);
    }

    public function isTimeType(): bool
    {
        return in_array($this->type, ['time', 'timeTz']);
    }
}
