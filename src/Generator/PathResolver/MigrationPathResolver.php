<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Illuminate\Support\Str;

class MigrationPathResolver extends PathResolver implements PathResolverInterface
{
    public function folderPath(): string
    {
        return database_path('migrations');
    }

    public function fileName(): string
    {
        $migrationClass = 'create_' . Str::plural(Str::snake($this->model)) . '_table';

        return date('Y_m_d_His') . "_{$migrationClass}.php";
    }
}
