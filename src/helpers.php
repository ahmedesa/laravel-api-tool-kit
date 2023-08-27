<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

if ( ! function_exists('dateTimeFormat')) {
    function dateTimeFormat(?string $value, ?string $format): ?string
    {
        $format ??= config('api-tool-kit.datetime_format');

        return $value ? Carbon::parse($value)->format($format) : null;
    }
}

if ( ! function_exists('createOrRandomFactory')) {
    function createOrRandomFactory($class_name): Model
    {
        $class = new $class_name();

        if ($class::count()) {
            return $class::inRandomOrder()->first();
        }

        return $class_name::factory()->create();
    }
}
