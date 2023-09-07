<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class ResourcePathResolver extends PathResolver
{
    public function folderPath(): string
    {
        return app_path("Http/Resources/{$this->model}");
    }

    public function fileName(): string
    {
        return "{$this->model}Resource.php";
    }
}
