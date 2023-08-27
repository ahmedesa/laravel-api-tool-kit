<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

if ( ! function_exists('dateTimeFormat')) {
    function dateTimeFormat(?string $value, ?string $format = null): ?string
    {
        $format ??= config('api-tool-kit.datetime_format');

        return $value ? Carbon::parse($value)->format($format) : null;
    }
}

if ( ! function_exists('createOrRandomFactory')) {
    function createOrRandomFactory($className): Model
    {
        $class = new $className();

        if ($class::count()) {
            return $class::inRandomOrder()->first();
        }

        return $className::factory()->create();
    }
}
