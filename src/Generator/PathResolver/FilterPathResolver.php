<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class FilterPathResolver extends PathResolver
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
