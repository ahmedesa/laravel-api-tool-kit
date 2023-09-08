<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathHasClass;

class ResourcePathResolver extends PathResolver implements PathHasClass
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

    public function getClassName(): string
    {
        return "{$this->model}Resource";
    }
}
