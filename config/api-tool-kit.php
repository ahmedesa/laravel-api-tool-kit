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
    | API Generators Commands
    |--------------------------------------------------------------------------
    |
    | Define API generator commands and their path resolvers.
    |
    */
    'api_generators' => [
        'options' => [
            'model' => [
                'command' => \Essa\APIToolKit\Generator\Commands\ModelGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\ModelPathResolver::class
            ],
            'factory' => [
                'command' => \Essa\APIToolKit\Generator\Commands\FactoryGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\FactoryPathResolver::class
            ],
            'seeder' => [
                'command' => \Essa\APIToolKit\Generator\Commands\SeederGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\SeederPathResolver::class
            ],
            'controller' => [
                'command' => \Essa\APIToolKit\Generator\Commands\ControllerGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\ControllerPathResolver::class
            ],
            'resource' => [
                'command' => \Essa\APIToolKit\Generator\Commands\ResourceGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\ResourcePathResolver::class
            ],
            'test' => [
                'command' => \Essa\APIToolKit\Generator\Commands\TestGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\TestPathResolver::class
            ],
            'update-request' => [
                'command' => \Essa\APIToolKit\Generator\Commands\CreateFormRequestGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\CreateFormRequestPathResolver::class
            ],
            'create-request' => [
                'command' => \Essa\APIToolKit\Generator\Commands\UpdateFormRequestGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\UpdateFormRequestPathResolver::class
            ],
            'filter' => [
                'command' => \Essa\APIToolKit\Generator\Commands\FilterGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\FilterPathResolver::class
            ],
            'migration' => [
                'command' => \Essa\APIToolKit\Generator\Commands\MigrationGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\MigrationPathResolver::class
            ],
            'routes' => [
                'command' => \Essa\APIToolKit\Generator\Commands\RoutesGeneratorCommand::class,
                'path-resolver' => \Essa\APIToolKit\Generator\PathResolver\RoutesPathResolver::class
            ],
        ]
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
    | Filters
    |--------------------------------------------------------------------------
    |
    | Specify the list of handler classes for processing query filters.
    | These handlers will be applied in the specified order.
    */
    'filters' => [
        'handlers' => [
            \Essa\APIToolKit\Filters\Handlers\FiltersHandler::class,
            \Essa\APIToolKit\Filters\Handlers\SortHandler::class,
            \Essa\APIToolKit\Filters\Handlers\IncludesHandler::class,
            \Essa\APIToolKit\Filters\Handlers\SearchHandler::class,
        ],
    ],
];
