<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Generators Commands
    |--------------------------------------------------------------------------
    |
    | Define API generator commands.
    |
    */
    'api_generator_commands' => [
        'model' => Essa\APIToolKit\Generator\Commands\ModelGeneratorCommand::class,
        'factory' => Essa\APIToolKit\Generator\Commands\FactoryGeneratorCommand::class,
        'seeder' => Essa\APIToolKit\Generator\Commands\SeederGeneratorCommand::class,
        'controller' => Essa\APIToolKit\Generator\Commands\ControllerGeneratorCommand::class,
        'resource' => Essa\APIToolKit\Generator\Commands\ResourceGeneratorCommand::class,
        'test' => Essa\APIToolKit\Generator\Commands\TestGeneratorCommand::class,
        'create-request' => Essa\APIToolKit\Generator\Commands\CreateFormRequestGeneratorCommand::class,
        'update-request' => Essa\APIToolKit\Generator\Commands\UpdateFormRequestGeneratorCommand::class,
        'filter' => Essa\APIToolKit\Generator\Commands\FilterGeneratorCommand::class,
        'migration' => Essa\APIToolKit\Generator\Commands\MigrationGeneratorCommand::class,
        'routes' => Essa\APIToolKit\Generator\Commands\RoutesGeneratorCommand::class,
    ],
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
