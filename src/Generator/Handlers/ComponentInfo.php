<?php

namespace Essa\APIToolKit\Generator\Handlers;

class ComponentInfo
{
    public string $type;
    public string $folder;
    public string $path;
    public string $stub;

    public function __construct(string $type, string $folder, string $path, string $stub)
    {
        $this->type = $type;
        $this->folder = $folder;
        $this->path = $path;
        $this->stub = $stub;
    }
}
