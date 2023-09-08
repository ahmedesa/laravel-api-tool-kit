<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathHasClass;

class FactoryPathResolver extends PathResolver implements PathHasClass
{
    public function folderPath(): string
    {
        return database_path('factories');
    }

    public function fileName(): string
    {
        return "{$this->model}Factory.php";
    }

    public function getNameSpace(): string
    {
        return 'Database\Factories';
    }

    public function getClassName(): string
    {
        return "{$this->model}Factory";
    }
}
