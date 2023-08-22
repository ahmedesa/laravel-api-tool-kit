<?php

namespace Essa\APIToolKit\Enum;

use ReflectionClass;

abstract class Enum
{
    /**
     * get values of every const at enum.
     */
    final public static function getAll(): array
    {
        return array_values(static::toArray());
    }

    final public static function toArray(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }

    final public static function getConst(): array
    {
        return array_keys(static::toArray());
    }

    final public static function isValid($value): bool
    {
        return in_array($value, static::toArray());
    }

    final public static function isValidConst($value): bool
    {
        return in_array($value, static::getConst());
    }

    final public static function getValue($const): mixed
    {
        return static::toArray()[$const];
    }
}
