<?php

namespace Essa\APIToolKit\Generator\PathResolver;

class UpdateFormRequestPathResolver extends CreateFormRequestPathResolver
{
    public function fileName(): string
    {
        return "Update{$this->model}Request.php";
    }

    public function getClassName(): string
    {
        return "Update{$this->model}Request";
    }
}
