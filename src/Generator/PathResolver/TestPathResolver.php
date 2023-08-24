<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

class TestPathResolver extends PathResolver implements PathResolverInterface
{
    public function folderPath(): string
    {
        return base_path('tests/Feature');
    }

    public function fileName(): string
    {
        return "{$this->model}Test.php";
    }
}
