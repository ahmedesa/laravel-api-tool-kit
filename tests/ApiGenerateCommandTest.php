<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Generator\PathResolver\ControllerPathResolver;
use Essa\APIToolKit\Generator\PathResolver\CreateFormRequestPathResolver;
use Essa\APIToolKit\Generator\PathResolver\FactoryPathResolver;
use Essa\APIToolKit\Generator\PathResolver\FilterPathResolver;
use Essa\APIToolKit\Generator\PathResolver\MigrationPathResolver;
use Essa\APIToolKit\Generator\PathResolver\ModelPathResolver;
use Essa\APIToolKit\Generator\PathResolver\ResourcePathResolver;
use Essa\APIToolKit\Generator\PathResolver\RoutesPathResolver;
use Essa\APIToolKit\Generator\PathResolver\SeedPathResolver;
use Essa\APIToolKit\Generator\PathResolver\TestPathResolver;
use Essa\APIToolKit\Generator\PathResolver\UpdateFormRequestPathResolver;

class ApiGenerateCommandTest extends TestCase
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
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            '--all' => true,
        ])
            ->assertExitCode(0);

        $this->assertFileExists((new ModelPathResolver($model))->getFullPath());
        $this->assertFileExists((new ControllerPathResolver($model))->getFullPath());
        $this->assertFileExists((new ResourcePathResolver($model))->getFullPath());
        $this->assertFileExists((new CreateFormRequestPathResolver($model))->getFullPath());
        $this->assertFileExists((new UpdateFormRequestPathResolver($model))->getFullPath());
        $this->assertFileExists((new FilterPathResolver($model))->getFullPath());
        $this->assertFileExists((new SeedPathResolver($model))->getFullPath());
        $this->assertFileExists((new FactoryPathResolver($model))->getFullPath());
        $this->assertFileExists((new TestPathResolver($model))->getFullPath());
        $this->assertFileExists((new RoutesPathResolver($model))->getFullPath());
        $this->assertFileExists((new MigrationPathResolver($model))->getFullPath());


        $this->assertStringContainsString(
            "Route::apiResource('/generatedModels'",
            file_get_contents((new RoutesPathResolver($model))->getFullPath())
        );
    }

    /**
     * @test
     */
    public function generateCommandWithSchemaShouldGenerateModelWithFillableAndRelations(): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            'schema' => "username:string:default('ahmed'),email:string:unique,company_data_id:foreignId:cascadeOnDelete",
        ])
            ->assertExitCode(0);

        $generatedModelContent = file_get_contents((new ModelPathResolver($model))->getFullPath());

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
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            'schema' => "username:string:default('ahmed'),email:string:unique,company_id:foreignId:cascadeOnDelete",
            '--migration' => true,
        ])
            ->assertExitCode(0);

        $migrationContent = file_get_contents((new MigrationPathResolver($model))->getFullPath());

        $this->assertStringContainsString('$table->string(\'username\')->default(\'ahmed\');', $migrationContent);
        $this->assertStringContainsString('$table->string(\'email\')->unique();', $migrationContent);
        $this->assertStringContainsString('$table->foreignId(\'company_id\')->constrained(\'companies\')->cascadeOnDelete();', $migrationContent);
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateFactory(): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            'schema' => "username:string:default('ahmed'),code:integer:unique,company_data_id:foreignId:cascadeOnDelete",
            '--factory' => true,
        ])
            ->assertExitCode(0);

        $factoryContent = file_get_contents((new FactoryPathResolver($model))->getFullPath());

        $this->assertStringContainsString('$this->faker->firstName()', $factoryContent);
        $this->assertStringContainsString('$this->faker->randomNumber()', $factoryContent);
        $this->assertStringContainsString('$this->faker->randomNumber()', $factoryContent);
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateResource(): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            'schema' => "username:string:default('ahmed'),email:string:unique,company_data_id:foreignId:cascadeOnDelete",
            '--all' => true,
        ])
            ->assertExitCode(0);

        $resourceContent = file_get_contents((new ResourcePathResolver($model))->getFullPath());

        $this->assertStringContainsString('$this->username', $resourceContent);
        $this->assertStringContainsString('$this->email', $resourceContent);
        $this->assertStringContainsString('$this->company_data_id', $resourceContent);
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateRequests(): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            'schema' => "username:string:default('ahmed'),email:string:unique,company_data_id:foreignId:cascadeOnDelete",
            '--request' => true,
        ])
            ->assertExitCode(0);

        $createRequestContent = file_get_contents((new CreateFormRequestPathResolver($model))->getFullPath());
        $updateRequestContent = file_get_contents((new UpdateFormRequestPathResolver($model))->getFullPath());

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
        $model = 'CustomSoftDeleteModel';

        $this->artisan('api:generate', [
            'model' => 'CustomSoftDeleteModel',
            '--all' => true,
            '--soft-delete' => true
        ])
            ->assertExitCode(0);

        $this->assertStringContainsString('SoftDeletes', file_get_contents((new ModelPathResolver($model))->getFullPath()));
        $this->assertStringContainsString('permanent-delete', file_get_contents((new RoutesPathResolver($model))->getFullPath()));
        $this->assertStringContainsString('restore', file_get_contents((new RoutesPathResolver($model))->getFullPath()));
        $this->assertStringContainsString('forceDelete', file_get_contents((new ControllerPathResolver($model))->getFullPath()));
    }

    public function generateCommandWithoutDefaultOptionsButWithoutSoftDelete(): void
    {
        $model = 'CustomModel';

        $this->artisan('api:generate', [
            'model' => 'CustomModel',
            '--all' => true,
        ])
            ->assertExitCode(0);

        $this->assertStringNotContainsString('SoftDeletes', file_get_contents((new ModelPathResolver($model))->getFullPath()));
        $this->assertStringNotContainsString('permanent-delete', file_get_contents((new RoutesPathResolver($model))->getFullPath()));
        $this->assertStringNotContainsString('restore', file_get_contents((new RoutesPathResolver($model))->getFullPath()));
        $this->assertStringNotContainsString('forceDelete', file_get_contents((new ControllerPathResolver($model))->getFullPath()));
    }

    /**
     * @test
     */
    public function generateCommandWithoutDefaultOptions(): void
    {
        $model = 'WithoutDefaultNewCustomModel';

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

        $this->assertFileExists((new ModelPathResolver($model))->getFullPath());
        $this->assertFileExists((new ResourcePathResolver($model))->getFullPath());
        $this->assertFileExists((new SeedPathResolver($model))->getFullPath());
        $this->assertFileExists((new TestPathResolver($model))->getFullPath());

        $this->assertFileDoesNotExist((new ControllerPathResolver($model))->getFullPath());
        $this->assertFileDoesNotExist((new FilterPathResolver($model))->getFullPath());
        $this->assertFileDoesNotExist((new FactoryPathResolver($model))->getFullPath());
    }
}
