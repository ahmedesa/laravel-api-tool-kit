<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

if ( ! function_exists('dateTimeFormat')) {
    /**
     * Format a datetime value using Carbon according to the specified format.
     *
     * @param string|null $value The datetime value to format.
     * @param string|null $format The optional format to use. If not provided, the default format from the configuration will be used.
     * @return string|null The formatted datetime value or null if the input value is null.
     */
    function dateTimeFormat(?string $value, ?string $format = null): ?string
    {
        $format ??= config('api-tool-kit.datetime_format');

        return $value ? Carbon::parse($value)->format($format) : null;
    }
}

if ( ! function_exists('createOrRandomFactory')) {
    /**
     * Create a new instance of a model using its factory if no records exist, or return a random record if records exist.
     *
     * @param string $className The name of the Eloquent model class.
     * @return Model The created model instance or a random model instance.
     */
    function createOrRandomFactory(string $className): Model
    {
        /** @var Illuminate\Database\Eloquent\Builder $class */
        $class = new $className();

        if ($class::count()) {
            return $class::inRandomOrder()->first();
        }

        /** @var Model $className */
        return $className::factory()->create();
    }
}
