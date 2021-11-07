<?php

namespace essa\APIToolKit;

use Illuminate\Support\ServiceProvider;
use essa\APIToolKit\Commands\MakeEnumCommand;
use essa\APIToolKit\Commands\GeneratorCommand;
use essa\APIToolKit\Commands\MakeActionCommand;
use essa\APIToolKit\Commands\MakeFilterCommand;
use essa\APIToolKit\Commands\GeneratePermissions;

class APIToolKitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    public function boot()
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
                MakeFilterCommand::class
            ]);
        }
    }
}
