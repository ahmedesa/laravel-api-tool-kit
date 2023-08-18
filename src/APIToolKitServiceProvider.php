<?php

namespace Essa\APIToolKit;

use Essa\APIToolKit\Commands\GeneratePermissions;
use Essa\APIToolKit\Commands\GeneratorCommand;
use Essa\APIToolKit\Commands\MakeActionCommand;
use Essa\APIToolKit\Commands\MakeEnumCommand;
use Essa\APIToolKit\Commands\MakeFilterCommand;
use Illuminate\Support\ServiceProvider;

class APIToolKitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->AddConfigFiles();

        $this->registerCommands();
    }

    public function AddConfigFiles(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/api-tool-kit.php', 'api-tool-kit');

        if ($this->app->runningInConsole() && function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/../config/api-tool-kit.php' => config_path('api-tool-kit.php'),
            ], 'config');
        }
    }

    public function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GeneratorCommand::class,
                MakeActionCommand::class,
                MakeEnumCommand::class,
                GeneratePermissions::class,
                MakeFilterCommand::class,
            ]);
        }
    }
}
