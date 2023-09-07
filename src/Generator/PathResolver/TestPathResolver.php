<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class TestPathResolver extends PathResolver
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
