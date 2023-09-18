<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\ClassInfoInterface;

class FilterPathResolver extends PathResolver implements ClassInfoInterface
{
    public function folderPath(): string
    {
        return app_path('Filters');
    }

    public function fileName(): string
    {
        return "{$this->model}Filters.php";
    }

    public function getNameSpace(): string
    {
        return 'App\Filters';
    }
}
