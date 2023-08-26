<?php

use Carbon\Carbon;

if ( ! function_exists('dateTimeFormat')) {
    function dateTimeFormat(?string $value, ?string $format): ?string
    {
        $format ??= config('api-tool-kit.datetime_format');

        return $value ? Carbon::parse($value)->format($format) : null;
    }
}
