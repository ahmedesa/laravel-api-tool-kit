<?php

namespace Essa\APIToolKit\Tests;

use Carbon\Carbon;
use Essa\APIToolKit\Tests\Mocks\Models\SluggableTestModel;
use Essa\APIToolKit\Tests\Mocks\Models\TestModel;
use Illuminate\Http\Request;

class FiltersTest extends TestCase
{
    /**
     * @test
     */
    public function generateFiltersClass(): void
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
    public function useFilterClassToSearchForColumn(): void
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
    public function useFilterClassToFilterByColumn(): void
    {
        TestModel::factory(5)->create();

        $this->app->bind('request', fn () => new Request([
            'id' => '2',
        ]));

        $records = TestModel::useFilters()->get();

        $this->assertCount(1, $records);
        $this->assertEquals(2, $records->first()->id);
    }

    public function useFilterClassToSortDateAscending(): void
    {
        TestModel::factory()->create([
            'created_at' => Carbon::parse('2023-08-01'),
        ]);
        TestModel::factory()->create([
            'created_at' => Carbon::parse('2023-08-02'),
        ]);

        $this->app->bind('request', fn () => new Request([
            'sorts' => 'created_at',
        ]));

        $records = TestModel::useFilters()->get();

        $this->assertTrue($records[0]->created_at <= $records[1]->created_at);
    }

    /**
     * @test
     */
    public function useFilterClassWithSortsAsc(): void
    {
        TestModel::factory()->create([
            'name' => 'Zebra',
        ]);
        TestModel::factory()->create([
            'name' => 'Apple',
        ]);

        $this->app->bind('request', fn () => new Request([
            'sorts' => 'name',
        ]));

        $records = TestModel::useFilters()->get();

        $this->assertEquals('Apple', $records->first()->name);
    }

    /**
     * @test
     */
    public function useFilterClassWithSortsDesc(): void
    {
        TestModel::factory()->create([
            'name' => 'Zebra',
        ]);
        TestModel::factory()->create([
            'name' => 'Apple',
        ]);

        $this->app->bind('request', fn () => new Request([
            'sorts' => '-name',
        ]));

        $records = TestModel::useFilters()->get();

        $this->assertEquals('Zebra', $records->first()->name);
    }

    /**
     * @test
     */
    public function useFilterClassWithCustomFilter(): void
    {
        TestModel::factory(2)->create([
            'created_at' => '2023-01-01',
        ]);
        TestModel::factory(3)->create([
            'created_at' => '2011-02-01',
        ]);

        TestModel::factory(7)->create([
            'created_at' => '2022-02-01',
        ]);

        $this->app->bind('request', fn () => new Request([
            'year' => 2022,
        ]));

        $records = TestModel::useFilters()->get();

        $this->assertCount(7, $records);
    }

    /**
     * @test
     */
    public function useFilterClassWithRelationSearch(): void
    {
        $modelWithOutRelation = TestModel::factory()->create([
            'name' => 'Parent Model',
        ]);

        $modelWithRelation = TestModel::factory()->create([
            'name' => 'Parent Model',
        ]);

        SluggableTestModel::create([
            'name' => 'Child Model',
            'test_model_id' => $modelWithRelation->id,
        ]);

        $this->app->bind('request', fn () => new Request([
            'search' => 'ild',
        ]));

        $records = TestModel::useFilters()->get();

        $this->assertCount(1, $records);
    }

    /**
     * @test
     */
    public function useFilterClassWithInclude(): void
    {
        $model = TestModel::factory()->create();

        SluggableTestModel::create([
            'name' => 'Included Model',
            'test_model_id' => $model->id,
        ]);

        $this->app->bind('request', fn () => new Request([
            'includes' => 'sluggableTestModel',
        ]));

        $records = TestModel::useFilters()->get();

        $this->assertCount(1, $records);
        $this->assertTrue($records->first()->relationLoaded('sluggableTestModel'));
    }
}
