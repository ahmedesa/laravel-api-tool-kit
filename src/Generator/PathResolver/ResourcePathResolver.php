<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

class ResourcePathResolver extends PathResolver implements PathResolverInterface
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
