<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathHasClass;

class ControllerPathResolver extends PathResolver implements PathHasClass
{
    public function folderPath(): string
    {
        return app_path('Http/Controllers/API');
    }

    public function fileName(): string
    {
        return "{$this->model}Controller.php";
    }

    public function getNameSpace(): string
    {
        return 'App\Http\Controllers\API';
    }

    public function getClassName(): string
    {
        return "{$this->model}Controller";
    }
}
