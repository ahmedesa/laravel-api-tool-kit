<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

class CreateFormRequestPathResolver extends PathResolver implements PathResolverInterface
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
