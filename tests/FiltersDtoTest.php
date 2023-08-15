<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Filters\DTO\FiltersDTO;
use Essa\APIToolKit\Tests\Mocks\Models\TestModel;
use Illuminate\Http\Request;

class FiltersDtoTest extends TestCase
{
    /**
     * @test
     */
    public function createFiltersDTOFromRequest()
    {
        $request = new Request([
            'sorts' => 'created_at',
            'color' => 'red',
            'includes' => 'model',
            'search' => 'keyword',
        ]);
        $filtersDTO = FiltersDTO::buildFromRequest($request);

        $this->assertInstanceOf(FiltersDTO::class, $filtersDTO);
        $this->assertEquals('created_at', $filtersDTO->getSorts());
        $this->assertEquals(['color' => 'red', 'search' => 'keyword'], $filtersDTO->getFilters());
        $this->assertEquals(['model'], $filtersDTO->getIncludes());
        $this->assertEquals('keyword', $filtersDTO->getSearch());
    }

    /**
     * @test
     */
    public function useFilterClassWithCustomFiltersDTO()
    {
        TestModel::factory()->create([
            'name' => 'Car',
            'created_at' => '2023-01-01',
        ]);

        $filtersDTO = new FiltersDTO(
            'created_at',
            ['name' => 'Car'],
            ['sluggableTestModel'],
            'Car'
        );

        $records = TestModel::useFilters(null, $filtersDTO)->get();

        $this->assertCount(1, $records);
        $this->assertTrue($records->pluck('name')->contains('Car'));
    }
}
