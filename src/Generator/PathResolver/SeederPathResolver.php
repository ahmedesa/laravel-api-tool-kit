<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\HasClassAndNamespace;

class SeederPathResolver extends PathResolver implements HasClassAndNamespace
{
    public function folderPath(): string
    {
        return database_path('seeders');
    }

    public function fileName(): string
    {
        return "{$this->model}Seeder.php";
    }

    public function getNameSpace(): string
    {
        return 'Database\Seeders';
    }
}
