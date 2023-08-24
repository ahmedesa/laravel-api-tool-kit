<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

class FactoryPathResolver extends PathResolver implements PathResolverInterface
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
