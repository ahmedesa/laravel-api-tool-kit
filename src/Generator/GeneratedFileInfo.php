<?php

namespace Essa\APIToolKit\Generator;

class GeneratedFileInfo
{
    public function __construct(private string $fileName, private string $folderPath, private ?string $namespace)
    {
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFolderPath(): string
    {
        return $this->folderPath;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function getFullPath(): string
    {
        return $this->folderPath . '/' . $this->fileName;
    }

    public function getClassName(): string
    {
        return str_replace('.php', '', $this->fileName);
    }
}
