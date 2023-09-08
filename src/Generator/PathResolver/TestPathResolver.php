<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathHasClass;
use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

class TestPathResolver extends PathResolver implements PathHasClass
{
    public function folderPath(): string
    {
        return base_path('tests/Feature');
    }

    public function fileName(): string
    {
        return "{$this->model}Test.php";
    }

    public function getNameSpace(): string
    {
        return 'Tests\Feature';
    }

    public function getClassName(): string
    {
        return "{$this->model}Test";
    }
}
