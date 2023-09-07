<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class ControllerPathResolver extends PathResolver
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
