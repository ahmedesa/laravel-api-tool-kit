<?php

use Essa\APIToolKit\Enum\GeneratorFilesType;

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
        GeneratorFilesType::MODEL->value => Essa\APIToolKit\Generator\Commands\ModelGeneratorCommand::class,
        GeneratorFilesType::FACTORY->value => Essa\APIToolKit\Generator\Commands\FactoryGeneratorCommand::class,
        GeneratorFilesType::SEEDER->value => Essa\APIToolKit\Generator\Commands\SeederGeneratorCommand::class,
        GeneratorFilesType::CONTROLLER->value => Essa\APIToolKit\Generator\Commands\ControllerGeneratorCommand::class,
        GeneratorFilesType::RESOURCE->value => Essa\APIToolKit\Generator\Commands\ResourceGeneratorCommand::class,
        GeneratorFilesType::TEST->value => Essa\APIToolKit\Generator\Commands\TestGeneratorCommand::class,
        GeneratorFilesType::CREATE_REQUEST->value => Essa\APIToolKit\Generator\Commands\CreateFormRequestGeneratorCommand::class,
        GeneratorFilesType::UPDATE_REQUEST->value => Essa\APIToolKit\Generator\Commands\UpdateFormRequestGeneratorCommand::class,
        GeneratorFilesType::FILTER->value => Essa\APIToolKit\Generator\Commands\FilterGeneratorCommand::class,
        GeneratorFilesType::MIGRATION->value => Essa\APIToolKit\Generator\Commands\MigrationGeneratorCommand::class,
        GeneratorFilesType::ROUTES->value => Essa\APIToolKit\Generator\Commands\RoutesGeneratorCommand::class,
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
