<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\HasClassAndNamespace;

class TestPathResolver extends PathResolver implements HasClassAndNamespace
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
}
