<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

class ControllerPathResolver extends PathResolver implements PathResolverInterface
{
    public function folderPath(): string
    {
        return app_path('Http/Controllers/API');
    }

    public function fileName(): string
    {
        return "{$this->model}Controller.php";
    }
}
