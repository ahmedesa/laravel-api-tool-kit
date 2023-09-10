<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\HasClassAndNamespace;

class FactoryPathResolver extends PathResolver implements HasClassAndNamespace
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
