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

    /*
    |--------------------------------------------------------------------------
    | Default Path Groups
    |--------------------------------------------------------------------------
    |
    | Define the default generator group that will be used by default when
    | no specific group is specified. Users can still create and use custom
    | groups in addition to this default group.
    |
    */
    'default_path_groups' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Generator Path Resolvers
    |--------------------------------------------------------------------------
    |
    | Define path resolvers for various generator types within custom groups.
    | Users can assign classes to each generator type for both 'default' and
    | custom groups.
    |
    */
    'generator_path_groups' => [
        'default' => [
            'model' => Essa\APIToolKit\Generator\PathResolver\ModelPathResolver::class,
            'factory' => Essa\APIToolKit\Generator\PathResolver\FactoryPathResolver::class,
            'seeder' => Essa\APIToolKit\Generator\PathResolver\SeederPathResolver::class,
            'controller' => Essa\APIToolKit\Generator\PathResolver\ControllerPathResolver::class,
            'resource' => Essa\APIToolKit\Generator\PathResolver\ResourcePathResolver::class,
            'test' => Essa\APIToolKit\Generator\PathResolver\TestPathResolver::class,
            'create-request' => Essa\APIToolKit\Generator\PathResolver\CreateFormRequestPathResolver::class,
            'update-request' => Essa\APIToolKit\Generator\PathResolver\UpdateFormRequestPathResolver::class,
            'filter' => Essa\APIToolKit\Generator\PathResolver\FilterPathResolver::class,
            'migration' => Essa\APIToolKit\Generator\PathResolver\MigrationPathResolver::class,
            'routes' => Essa\APIToolKit\Generator\PathResolver\RoutesPathResolver::class,
        ],
    ],
];
