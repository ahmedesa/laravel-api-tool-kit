<?php

namespace Essa\APIToolKit\Enum;

/**
 * Trait EnumHelpers
 *
 * Provides utility methods for native PHP 8.1+ BackedEnum types.
 * This is the modern replacement for the deprecated Enum base class.
 *
 * Usage:
 *   enum Status: string {
 *       use \Essa\APIToolKit\Enum\EnumHelpers;
 *       case Active = 'active';
 *       case Inactive = 'inactive';
 *   }
 *
 *   Status::values();            // ['active', 'inactive']
 *   Status::names();             // ['Active', 'Inactive']
 *   Status::toArray();           // ['Active' => 'active', 'Inactive' => 'inactive']
 *   Status::isValid('active');   // true
 *   Status::isValid('unknown');  // false
 *
 * @package Essa\APIToolKit\Enum
 */
trait EnumHelpers
{
    /**
     * Get all case values of the enum.
     *
     * @return array<int, string|int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all case names of the enum.
     *
     * @return array<int, string>
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Convert the enum to an associative array of names to values.
     *
     * @return array<string, string|int>
     */
    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'name'),
            array_column(self::cases(), 'value')
        );
    }

    /**
     * Check if a given value is valid for this enum.
     *
     * @param mixed $value The value to check.
     * @return bool
     */
    public static function isValid(mixed $value): bool
    {
        return null !== self::tryFrom($value);
    }

    /**
     * Get the enum case for a given value, or null if not found.
     *
     * @param string|int $value
     * @return static|null
     */
    public static function fromValue(string|int $value): ?static
    {
        return self::tryFrom($value);
    }
}
