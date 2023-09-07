<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class CreateFormRequestPathResolver extends PathResolver
{
    public function folderPath(): string
    {
        return app_path("Http/Requests/{$this->model}");
    }

    public function fileName(): string
    {
        return "Create{$this->model}Request.php";
    }
}
