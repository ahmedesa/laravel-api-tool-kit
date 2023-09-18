<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\ClassInfoInterface;

class ResourcePathResolver extends PathResolver implements ClassInfoInterface
{
    public function folderPath(): string
    {
        return app_path("Http/Resources/{$this->model}");
    }

    public function fileName(): string
    {
        return "{$this->model}Resource.php";
    }

    public function getNameSpace(): string
    {
        return "App\Http\Resources\\{$this->model}";
    }
}
