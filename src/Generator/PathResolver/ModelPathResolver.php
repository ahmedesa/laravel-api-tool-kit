<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathHasClass;

class ModelPathResolver extends PathResolver implements PathHasClass
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

    public function getClassName(): string
    {
        return "{$this->model}";
    }
}
