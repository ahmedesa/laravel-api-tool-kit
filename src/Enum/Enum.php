<?php

namespace Essa\APIToolKit\Enum;

abstract class Enum
{
    /**
     * get values of every const at enum.
     *
     * @return array
     */
    final public static function getAll(): array
    {
        return array_values(static::toArray());
    }

    /**
     * @return array
     */
    final public static function toArray(): array
    {
        return (new \ReflectionClass(static::class))->getConstants();
    }

    /**
     * @return array
     */
    final public static function getConst(): array
    {
        return array_keys(static::toArray());
    }

    /**
     * @param $value
     * @return bool
     */
    final public static function isValid($value): bool
    {
        return in_array($value, static::toArray());
    }


    /**
     * @param $value
     * @return bool
     */
    final public static function isValidConst($value): bool
    {
        return in_array($value, static::getConst());
    }

    /**
     * @param $const
     * @return mixed
     */
    final public static function getValue($const)
    {
        return static::toArray()[$const];
    }
}
