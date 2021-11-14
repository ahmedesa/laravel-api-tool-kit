<?php

namespace Essa\APIToolKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GeneratePermissions extends Command
{
    protected $signature = 'generate:permissions {model}';

    protected $description = 'Generate permissions for model';

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    public function handle()
    {
        if (! file_exists(database_path("permissions.csv"))) {
            file_put_contents(
                database_path("permissions.csv"),
                file_get_contents(__DIR__ . '/../permissions.csv')
            );
        }

        if (! file_exists(config_path("permissions-map") . ".php")) {
            file_put_contents(
                config_path("permissions-map") . ".php",
                file_get_contents(__DIR__ . '/../../config/permissions-map.php')
            );
        }

        $permissions = [
            'index' => 'list',
            'store' => 'create',
            'show' => 'view',
            'destroy' => 'delete',
            'update' => 'update',
        ];

        $soft_delete_permission = [
            'restore' => 'force-delete',
            'permanentDelete' => 'force-delete',
        ];

        if (config('api-tool-kit.use_soft_delete')) {
            $permissions = $permissions + $soft_delete_permission;
        }

        $this->addPermissionsToConfigFile($permissions);

        $this->AddPermissionsToCsvFile(array_unique(array_values($permissions)));

        $this->info('permissions generated successfully!');
    }

    public function addPermissionsToConfigFile($permissions): void
    {
        $model = $this->argument('model');

        $new_value[$model] = $permissions;

        if (config('permissions-map')) {
            $new_value = $new_value + config('permissions-map');
        }

        $conf_file = config_path("permissions-map") . ".php";

        file_put_contents(
            $conf_file,
            "<?php \n return " . var_export($new_value, true) . ";"
        );
    }

    public function AddPermissionsToCsvFile(array $permissions): void
    {
        $model = $this->argument('model');

        $output = '';

        foreach ($permissions as $permission) {
            $output = $output . "$model.$permission\n";
        }

        $this->filesystem->append(
            database_path("permissions.csv"),
            $output
        );
    }
}

