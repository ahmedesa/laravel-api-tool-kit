<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class ModelPathResolver extends PathResolver
{
    public function folderPath(): string
    {
        return app_path('Models');
    }

    public function fileName(): string
    {
        return "{$this->model}.php";
    }
}
