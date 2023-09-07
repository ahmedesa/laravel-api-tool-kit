<?php

namespace Essa\APIToolKit\Generator\PathResolver;

use Illuminate\Support\Str;

class MigrationPathResolver extends PathResolver
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
