<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\APIToolKitServiceProvider;
use Essa\APIToolKit\MacroServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->createRoutesFile();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Essa\\APIToolKit\\Tests\\database\\factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    private function createRoutesFile(): void
    {
        $filePath = base_path('routes/api.php');

        if (is_dir($filePath)) {
            rmdir($filePath);
        }

        if (!file_exists($filePath)) {
            touch($filePath);
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            APIToolKitServiceProvider::class,
            MacroServiceProvider::class,
        ];
    }

    protected function setUpDatabase(Application $app): void
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table): void {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->string('code');
            $table->boolean('is_active')->default(true);
        });

        $app['db']->connection()->getSchemaBuilder()->create('sluggable_test_models', function (Blueprint $table): void {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('test_model_id')->nullable();
        });
    }


    protected function normalizeWhitespaceAndNewlines(string $content): string
    {
        $content = preg_replace('/\s+/', ' ', $content);
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        return trim($content);
    }
}
