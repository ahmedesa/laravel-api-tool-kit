<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\ClassInfoInterface;

class FactoryPathResolver extends PathResolver implements ClassInfoInterface
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
}
