<?php

namespace Essa\APIToolKit\Generator\DTOs;

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
