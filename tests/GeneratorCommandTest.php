<?php

namespace Essa\APIToolKit\Tests;

class GeneratorCommandTest extends TestCase
{
    /**
     * @test
     */
    public function generateCommandWithAllDefaults()
    {
        $this->artisan('api:generate', [
            'model' => 'GeneratedModel',
        ])
            ->expectsQuestion('Select all default options ?', 'y')
            ->expectsChoice('Do you want to use <options=bold>soft delete</> ?', 'y', ['Yes', 'No', 'n', 'y'])
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
    public function reservedNameValidation()
    {
        $this->artisan('api:generate', [
            'model' => 'class',
        ])
            ->expectsOutput('The name "class" is reserved by PHP.')
            ->assertExitCode(0);
    }
}
