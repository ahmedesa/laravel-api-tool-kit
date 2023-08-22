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
            '--all' => true,
        ])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Models/GeneratedModel.php'));
        $this->assertFileExists(app_path('Http/Controllers/API/GeneratedModelController.php'));
        $this->assertFileExists(app_path('Http/Resources/GeneratedModel/GeneratedModelResource.php'));
        $this->assertFileExists(app_path('Http/Requests/GeneratedModel/CreateGeneratedModelRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/GeneratedModel/UpdateGeneratedModelRequest.php'));
        $this->assertFileExists(app_path('Filters/GeneratedModelFilters.php'));
        $this->assertFileExists(database_path('seeders/GeneratedModelSeeder.php'));
        $this->assertFileExists(database_path('factories/GeneratedModelFactory.php'));
        $this->assertFileExists(base_path('tests/Feature/GeneratedModelTest.php'));
        $this->assertFileExists(base_path('routes/api.php'));
        $this->assertFileExists(database_path("migrations/" . date('Y_m_d_His') . "_create_generated_models_table.php"));

        $this->assertStringContainsString("Route::apiResource('/generatedModels'", file_get_contents(base_path('routes/api.php')));
    }

    /**
     * @test
     */
    public function generateCommandWithSchemaShouldGenerateModelWithFillableAndRelations(): void
    {
        $this->artisan('api:generate', [
            'model' => 'GeneratedModel',
            'schema' => "username:string:default('ahmed'),email:string:unique,company_data_id:foreignId:cascadeOnDelete",
        ])
            ->assertExitCode(0);

        $generatedModelContent = file_get_contents(app_path('Models/GeneratedModel.php'));

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines("protected \$fillable = [ 'username', 'email', 'company_data_id', ];"),
            $this->normalizeWhitespaceAndNewlines($generatedModelContent)
        );

        $this->assertStringContainsString('public function companyData(): \Illuminate\Database\Eloquent\Relations\BelongsTo', $generatedModelContent);
        $this->assertStringContainsString('return $this->belongsTo(\App\Models\CompanyData::class);', $generatedModelContent);

    }


    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateMigration(): void
    {
        $this->artisan('api:generate', [
            'model' => 'GeneratedModel',
            'schema' => "username:string:default('ahmed'),email:string:unique,company_id:foreignId:cascadeOnDelete",
            '--migration' => true,
        ])
            ->assertExitCode(0);

        $migrationContent = file_get_contents(database_path("migrations/" . date('Y_m_d_His') . "_create_generated_models_table.php"));

        $this->assertStringContainsString('$table->string(\'username\')->default(\'ahmed\');', $migrationContent);
        $this->assertStringContainsString('$table->string(\'email\')->unique();', $migrationContent);
        $this->assertStringContainsString('$table->foreignId(\'company_id\')->constrained(\'companies\')->cascadeOnDelete();', $migrationContent);
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateFactory(): void
    {
        $this->artisan('api:generate', [
            'model' => 'GeneratedModel',
            'schema' => "username:string:default('ahmed'),code:integer:unique,company_data_id:foreignId:cascadeOnDelete",
            '--all' => true,
        ])
            ->assertExitCode(0);

        $factoryFileName = database_path('factories/GeneratedModelFactory.php');
        $factoryContent = file_get_contents($factoryFileName);

        $this->assertStringContainsString('$this->faker->firstName', $factoryContent);
        $this->assertStringContainsString('$this->faker->randomNumber', $factoryContent);
        $this->assertStringContainsString('$this->faker->smallInteger', $factoryContent);
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateResource(): void
    {
        $this->artisan('api:generate', [
            'model' => 'GeneratedModel',
            'schema' => "username:string:default('ahmed'),email:string:unique,company_data_id:foreignId:cascadeOnDelete",
            '--all' => true,
        ])
            ->assertExitCode(0);

        $resourceFileName = app_path('Http/Resources/GeneratedModel/GeneratedModelResource.php');
        $resourceContent = file_get_contents($resourceFileName);

        $this->assertStringContainsString('$this->username', $resourceContent);
        $this->assertStringContainsString('$this->email', $resourceContent);
        $this->assertStringContainsString('$this->company_data_id', $resourceContent);
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateRequests(): void
    {
        $this->artisan('api:generate', [
            'model' => 'GeneratedModel',
            'schema' => "username:string:default('ahmed'),email:string:unique,company_data_id:foreignId:cascadeOnDelete",
            '--request' => true,
        ])
            ->assertExitCode(0);

        $createRequestContent = file_get_contents(app_path('Http/Requests/GeneratedModel/CreateGeneratedModelRequest.php'));
        $updateRequestContent = file_get_contents(app_path('Http/Requests/GeneratedModel/UpdateGeneratedModelRequest.php'));

        // Assertions for Create Request
        $this->assertStringContainsString('\'username\' => \'required\'', $createRequestContent);
        $this->assertStringContainsString('\'email\' => \'required\'', $createRequestContent);
        $this->assertStringContainsString('\'company_data_id\' => \'required\'', $createRequestContent);

        // Assertions for Update Request
        $this->assertStringContainsString('\'username\' => \'sometimes\'', $updateRequestContent);
        $this->assertStringContainsString('\'email\' => \'sometimes\'', $updateRequestContent);
        $this->assertStringContainsString('\'company_data_id\' => \'sometimes\'', $updateRequestContent);
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
