<?php

namespace Essa\APIToolKit\Tests;

class GeneratorCommandTest extends TestCase
{
    /**
     * @test
     */
    public function reservedNameValidation(): void
    {
        $this->artisan('api:generate', [
            'model' => 'class',
        ])
            ->expectsOutput('The name "class" is reserved by PHP.')
            ->assertExitCode(0);
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaults(): void
    {
        $this->artisan('api:generate', [
            'model' => 'GeneratedModel',
            'schema' =>"username:string:default('ahmed'),email:string:unique,company_id:foreignId:cascadeOnDelete:nullable",
            '--all' => true,
        ])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Models/GeneratedModel.php'));
        $this->assertFileExists(app_path('Http/Controllers/API/GeneratedModelController.php'));
        $this->assertFileExists(app_path('Http/Resources/GeneratedModel/GeneratedModelResource.php'));
        $this->assertFileExists(database_path('migrations'));
        $this->assertFileExists(database_path('factories/GeneratedModelFactory.php'));
        $this->assertFileExists(database_path('seeders/GeneratedModelSeeder.php'));
        $this->assertFileExists(app_path('Filters/GeneratedModelFilters.php'));
        $this->assertFileExists(base_path('tests/Feature/GeneratedModelTest.php'));
        $this->assertFileExists(base_path('routes/api.php'));

        $this->assertStringContainsString("Route::apiResource('/generatedModels'", file_get_contents(base_path('routes/api.php')));
    }

    /**
     * @test
     */
    public function generateCommandWithoutDefaultOptionsButWithSoftDelete(): void
    {
        $this->artisan('api:generate', [
            'model' => 'CustomSoftDeleteModel',
            '--all' => true,
            '--soft-delete' => true
        ])
            ->assertExitCode(0);

        $this->assertStringContainsString('SoftDeletes', file_get_contents(app_path('Models/CustomSoftDeleteModel.php')));
        $this->assertStringContainsString('permanent-delete', file_get_contents(base_path('routes/api.php')));
        $this->assertStringContainsString('restore', file_get_contents(base_path('routes/api.php')));
        $this->assertStringContainsString('forceDelete', file_get_contents(app_path('Http/Controllers/API/CustomSoftDeleteModelController.php')));
    }

    public function generateCommandWithoutDefaultOptionsButWithoutSoftDelete(): void
    {
        $this->artisan('api:generate', [
            'model' => 'CustomModel',
            '--all' => true,
        ])
            ->assertExitCode(0);

        $this->assertStringNotContainsString('SoftDeletes', file_get_contents(app_path('Models/CustomModel.php')));
        $this->assertStringNotContainsString('permanent-delete', file_get_contents(base_path('routes/api.php')));
        $this->assertStringNotContainsString('restore', file_get_contents(base_path('routes/api.php')));
        $this->assertStringNotContainsString('forceDelete', file_get_contents(app_path('Http/Controllers/API/CustomModelController.php')));
    }

    /**
     * @test
     */
    public function generateCommandWithoutDefaultOptions(): void
    {
        $this->artisan('api:generate', [
            'model' => 'WithoutDefaultNewCustomModel',
            '--soft-delete' => false,
            '--controller' => false,
            '--request' => false,
            '--resource' => true,
            '--migration' => true,
            '--factory' => false,
            '--seeder' => true,
            '--filter' => false,
            '--test' => true,
            '--routes' => true,
        ])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Models/WithoutDefaultNewCustomModel.php'));
        $this->assertFileExists(app_path('Http/Resources/WithoutDefaultNewCustomModel/WithoutDefaultNewCustomModelResource.php'));
        $this->assertFileExists(database_path('migrations'));
        $this->assertFileExists(database_path('seeders/WithoutDefaultNewCustomModelSeeder.php'));
        $this->assertFileExists(base_path('tests/Feature/WithoutDefaultNewCustomModelTest.php'));

        $this->assertFileDoesNotExist(app_path('Http/Controllers/API/WithoutDefaultNewCustomModelController.php'));
        $this->assertFileDoesNotExist(database_path('factories/WithoutDefaultNewCustomModelFactory.php'));
        $this->assertFileDoesNotExist(app_path('Filters/WithoutDefaultNewCustomModelFilters.php'));
    }
}
