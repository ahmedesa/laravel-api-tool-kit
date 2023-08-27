<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

class FilterPathResolver extends PathResolver implements PathResolverInterface
{
    public function folderPath(): string
    {
        return app_path('Filters');
    }

    public function fileName(): string
    {
        return "{$this->model}Filters.php";
    }
}
