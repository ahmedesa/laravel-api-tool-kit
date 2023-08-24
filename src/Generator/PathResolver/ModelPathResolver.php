<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

class ModelPathResolver extends PathResolver implements PathResolverInterface
{
    public function folderPath(): string
    {
        return app_path('/Models');
    }

    public function fileName(): string
    {
        return "{$this->model}.php";
    }
}
