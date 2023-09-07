<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;

abstract class PathResolver implements PathResolverInterface
{
    public function __construct(protected string $model)
    {
    }

    public function getFullPath(): string
    {
        return $this->folderPath() . '/' . $this->fileName();
    }
}
