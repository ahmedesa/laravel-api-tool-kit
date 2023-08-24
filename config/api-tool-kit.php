<?php

use Essa\APIToolKit\Generator\Commands\ControllerGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\CreateFormRequestGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\FactoryGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\FilterGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\MigrationGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\ModelGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\ResourceGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\RoutesGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\SeederGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\TestGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\UpdateFormRequestGeneratorCommand;
use Essa\APIToolKit\Generator\PathResolver\ControllerPathResolver;
use Essa\APIToolKit\Generator\PathResolver\CreateFormRequestPathResolver;
use Essa\APIToolKit\Generator\PathResolver\FactoryPathResolver;
use Essa\APIToolKit\Generator\PathResolver\FilterPathResolver;
use Essa\APIToolKit\Generator\PathResolver\MigrationPathResolver;
use Essa\APIToolKit\Generator\PathResolver\ModelPathResolver;
use Essa\APIToolKit\Generator\PathResolver\ResourcePathResolver;
use Essa\APIToolKit\Generator\PathResolver\RoutesPathResolver;
use Essa\APIToolKit\Generator\PathResolver\SeederPathResolver;
use Essa\APIToolKit\Generator\PathResolver\TestPathResolver;
use Essa\APIToolKit\Generator\PathResolver\UpdateFormRequestPathResolver;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Generators
    |--------------------------------------------------------------------------
    |
    | the default option that will be created if no option specified
    |
    | Supported options: 'seeder','controller','request','resource','factory',
    |                    'migration','filter','test','routes'
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

    'api_generators' => [
        'commands' => [
            [
                'option' => 'model',
                'command' => ModelGeneratorCommand::class,
                'path-resolver' => ModelPathResolver::class
            ],
            [
                'option' => 'factory',
                'command' => FactoryGeneratorCommand::class,
                'path-resolver' => FactoryPathResolver::class
            ],
            [
                'option' => 'seeder',
                'command' => SeederGeneratorCommand::class,
                'path-resolver' => SeederPathResolver::class
            ],
            [
                'option' => 'controller',
                'command' => ControllerGeneratorCommand::class,
                'path-resolver' => ControllerPathResolver::class
            ],
            [
                'option' => 'resource',
                'command' => ResourceGeneratorCommand::class,
                'path-resolver' => ResourcePathResolver::class
            ],
            [
                'option' => 'test',
                'command' => TestGeneratorCommand::class,
                'path-resolver' => TestPathResolver::class
            ],
            [
                'option' => 'request',
                'command' => CreateFormRequestGeneratorCommand::class,
                'path-resolver' => CreateFormRequestPathResolver::class
            ],
            [
                'option' => 'request',
                'command' => UpdateFormRequestGeneratorCommand::class,
                'path-resolver' => UpdateFormRequestPathResolver::class
            ],
            [
                'option' => 'filter',
                'command' => FilterGeneratorCommand::class,
                'path-resolver' => FilterPathResolver::class
            ],
            [
                'option' => 'migration',
                'command' => MigrationGeneratorCommand::class,
                'path-resolver' => MigrationPathResolver::class
            ],
            [
                'option' => 'routes',
                'command' => RoutesGeneratorCommand::class,
                'path-resolver' => RoutesPathResolver::class
            ],
        ]
    ],
    /*
    |--------------------------------------------------------------------------
    | Default Generators
    |--------------------------------------------------------------------------
    | number of items per page when use dynamic pagination
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
            Essa\APIToolKit\Filters\Handlers\FiltersHandler::class,
            Essa\APIToolKit\Filters\Handlers\SortHandler::class,
            Essa\APIToolKit\Filters\Handlers\IncludesHandler::class,
            Essa\APIToolKit\Filters\Handlers\SearchHandler::class,
        ],
    ],
];
