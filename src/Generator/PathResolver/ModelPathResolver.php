<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\ClassInfoInterface;

class ModelPathResolver extends PathResolver implements ClassInfoInterface
{
    public function folderPath(): string
    {
        return app_path('Models');
    }

    public function fileName(): string
    {
        return "{$this->model}.php";
    }

    public function getNameSpace(): string
    {
        return 'App\Models';
    }
}
