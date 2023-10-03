<?php

namespace Essa\APIToolKit\Enum;

class GeneratorFilesType extends Enum
{
    public const MODEL = 'model';

    public const CONTROLLER = 'controller';

    public const RESOURCE = 'resource';

    public const FACTORY = 'factory';

    public const SEEDER = 'seeder';

    public const TEST = 'test';

    public const FILTER = 'filter';

    public const MIGRATION = 'migration';

    public const ROUTES = 'routes';

    public const CREATE_REQUEST = 'create-request';

    public const UPDATE_REQUEST = 'update-request';
}
