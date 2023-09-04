<?php

namespace Essa\APIToolKit\Generator\PathResolver;

abstract class PathResolver
{
    public function __construct(protected string $model)
    {
    }

    public function getFullPath(): string
    {
        return $this->folderPath() . '/' . $this->fileName();
    }
}
