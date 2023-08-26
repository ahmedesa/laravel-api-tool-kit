<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Generators
    |--------------------------------------------------------------------------
    |
    | The default options that will be created if no option is specified.
    |
    | Supported options: 'seeder', 'controller', 'request', 'resource', 'factory',
    |                    'migration', 'filter', 'test', 'routes'
    |
    */
    'default_generates' => [
        'seeder',
        'controller',
        'request',
        'resource',
        'factory',
        'migration',
        'filter',
        'test',
        'routes',
    ],
    /*
    |--------------------------------------------------------------------------
    | Default Generators
    |--------------------------------------------------------------------------
    | Number of items per page when using dynamic pagination.
    */
    'default_pagination_number' => 20,

    /*
    |--------------------------------------------------------------------------
    | Default Datetime Format for API Resources
    |--------------------------------------------------------------------------
    | The default format for displaying date and time values in API resources.
    | Used by the dateTimeFormat function when generating API resource responses,
    | ensuring consistent formatting for datetime values.
    */
    'datetime_format' => 'Y-m-d H:i:s',
];
