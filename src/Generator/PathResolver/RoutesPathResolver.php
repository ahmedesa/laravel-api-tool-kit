<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class RoutesPathResolver extends PathResolver
{
    public function folderPath(): string
    {
        return base_path('routes');
    }

    public function fileName(): string
    {
        return 'api.php';
    }
}
