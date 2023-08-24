<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

class SeederPathResolver extends PathResolver implements PathResolverInterface
{
    public function folderPath(): string
    {
        return database_path('/seeders');
    }

    public function fileName(): string
    {
        return "{$this->model}Seeder.php";
    }
}
