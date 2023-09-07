<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class SeederPathResolver extends PathResolver
{
    public function folderPath(): string
    {
        return database_path('seeders');
    }

    public function fileName(): string
    {
        return "{$this->model}Seeder.php";
    }
}
