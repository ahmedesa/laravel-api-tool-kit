<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

class RoutesPathResolver extends PathResolver implements PathResolverInterface
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
