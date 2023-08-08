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
        TestModel::factory(5)->create([
            'name' => 'aaa',
        ]);

        TestModel::factory(7)->create([
            'name' => 'bbb',
        ]);

        $this->app->bind('request', fn () => new Request([
            'search' => 'a',
        ]));

        $records = TestModel::useFilters()->get();

        $this->assertCount(5, $records);
    }

    /**
     * @test
     */
    public function useFilterClassToFilterByColumn()
    {
        TestModel::factory(5)->create();

        $this->app->bind('request', fn () => new Request([
            'id' => '2',
        ]));

        $records = TestModel::useFilters()->get();

        $this->assertCount(1, $records);
        $this->assertEquals(2, $records->first()->id);
    }

    /**
     * @test
     */
    public function useFilterClassToSearchForNonexistentColumn()
    {
        TestModel::factory(5)->create([
            'name' => 'aaa',
        ]);

        TestModel::factory(7)->create([
            'name' => 'bbb',
        ]);

        $this->app->bind('request', fn () => new Request([
            'search' => 'c',
        ]));

        $records = TestModel::useFilters()->get();

        $this->assertCount(0, $records);
    }

    /**
     * @test
     */
    public function useFilterClassWithEmptyRequest()
    {
        TestModel::factory(5)->create([
            'name' => 'aaa',
        ]);

        TestModel::factory(7)->create([
            'name' => 'bbb',
        ]);

        $this->app->bind('request', fn () => new Request());

        $records = TestModel::useFilters()->get();

        $this->assertCount(12, $records);
    }
}