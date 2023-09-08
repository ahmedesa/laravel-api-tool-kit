<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathHasClass;

class SeederPathResolver extends PathResolver implements PathHasClass
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

    public function getClassName(): string
    {
        return "{$this->model}Seeder";
    }
}
