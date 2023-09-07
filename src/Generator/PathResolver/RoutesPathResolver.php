<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class RoutesPathResolver extends PathResolver
{
    public function folderPath(): string
    {
        return base_path('routes/api.php');
    }

    public function fileName(): string
    {
        return base_path('routes/api.php');
    }

    public function getFullPath(): string
    {
        return base_path('routes/api.php');
    }
}
