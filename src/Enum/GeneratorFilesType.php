<?php

declare(strict_types=1);

namespace Essa\APIToolKit\Enum;

enum GeneratorFilesType: string
{
    use EnumHelpers;

    case MODEL = 'model';
    case CONTROLLER = 'controller';
    case RESOURCE = 'resource';
    case FACTORY = 'factory';
    case SEEDER = 'seeder';
    case TEST = 'test';
    case FILTER = 'filter';
    case MIGRATION = 'migration';
    case ROUTES = 'routes';
    case CREATE_REQUEST = 'create-request';
    case UPDATE_REQUEST = 'update-request';
}
