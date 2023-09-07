<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class FactoryPathResolver extends PathResolver
{
    public function folderPath(): string
    {
        return database_path('factories');
    }

    public function fileName(): string
    {
        return "{$this->model}Factory.php";
    }
}
