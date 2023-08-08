<?php

use Essa\APIToolKit\Tests\Mocks\TestModel;
use Essa\APIToolKit\Tests\TestCase;
use Illuminate\Http\Request;

class FiltersTest extends TestCase
{
    /**
     * @test
     */
    public function generateFiltersClass()
    {
        $name = 'TestFilters';

        $this->artisan('make:filter', ['name' => $name])
            ->assertExitCode(0);

        $actionClassPath = app_path("Filters/{$name}.php");

        $this->assertFileExists($actionClassPath);

        $this->assertStringContainsString('namespace App\\Filters;', file_get_contents($actionClassPath));

        $this->assertStringContainsString("class {$name}", file_get_contents($actionClassPath));
    }

    /**
     * @test
     */
    public function useFilterClassToSearchForColumn()
    {
        // Existing test case
        // ...

        // Additional test case
        // ...

        // Additional test case
        // ...
    }

    /**
     * @test
     */
    public function useFilterClassToFilterByColumn()
    {
        // Existing test case
        // ...

        // Additional test case
        // ...
    }

    /**
     * @test
     */
    public function useFilterClassToSearchForNonexistentColumn()
    {
        // Existing test case
        // ...

        // Additional test case
        // ...
    }

    /**
     * @test
     */
    public function useFilterClassWithEmptyRequest()
    {
        // Existing test case
        // ...

        // Additional test case
        // ...
    }
}