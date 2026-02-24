<?php

declare(strict_types=1);

namespace Essa\APIToolKit\Enum;

trait EnumHelpers
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function isValid(mixed $value): bool
    {
        return is_string($value) && null !== self::tryFrom($value);
    }

    public static function fromValue(string $value): ?self
    {
        return self::tryFrom($value);
    }

    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'name'),
            array_column(self::cases(), 'value')
        );
    }
}
