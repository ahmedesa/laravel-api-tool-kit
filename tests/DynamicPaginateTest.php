<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Tests\Mocks\Models\TestModel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DynamicPaginateTest extends TestCase
{
    /**
     * @test
     */
    public function getAllRecordsWithDynamicPagination()
    {
        TestModel::factory(50)->create();

        $randomNumber = rand(1, 50);

        $this->app->bind('request', fn () => new Request([
            'per_page' => $randomNumber,
        ]));

        /** @var LengthAwarePaginator $paginatedRecords */
        $paginatedRecords = TestModel::dynamicPaginate();

        $this->assertCount($randomNumber, $paginatedRecords->all());
    }

    /**
     * @test
     */
    public function getAllRecordsWithoutPagination()
    {
        TestModel::factory(50)->create();

        $this->app->bind('request', fn () => new Request([
            'pagination' => 'none',
        ]));

        /** @var LengthAwarePaginator $paginatedRecords */
        $paginatedRecords = TestModel::dynamicPaginate();

        $this->assertCount(50, $paginatedRecords->all());
    }

    /**
     * @test
     */
    public function getRecordsWithDefaultPerPage()
    {
        $randomNumber = rand(1, 30);

        $this->app['config']->set('api-tool-kit.default_pagination_number', $randomNumber);

        TestModel::factory(30)->create();

        $this->app->bind('request', fn () => new Request());

        /** @var LengthAwarePaginator $paginatedRecords */
        $paginatedRecords = TestModel::dynamicPaginate();

        $this->assertCount($randomNumber, $paginatedRecords->all());
    }
}
