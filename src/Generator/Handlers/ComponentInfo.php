<?php

namespace Essa\APIToolKit\Generator\Handlers;

class ComponentInfo
{
    public function __construct(
        public string $type,
        public string $folder,
        public string $path,
        public string $stub
    ) {
    }
}
