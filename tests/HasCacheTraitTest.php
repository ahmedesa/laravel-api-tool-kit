<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Enum\CacheKeys;
use Essa\APIToolKit\Tests\Mocks\Models\TestModel;
use Illuminate\Support\Facades\Cache;

class HasCacheTraitTest extends TestCase
{
    /**
     * @test
     */
    public function flushCacheIsCalledOnModelUpdate()
    {
        Cache::put(CacheKeys::DEFAULT_CACHE_KEY, 'cached data', 60);

        TestModel::fireModelEvent('updated');

        $this->assertNull(Cache::get(CacheKeys::DEFAULT_CACHE_KEY));
    }

    /**
     * @test
     */
    public function flushCacheIsCalledOnModelCreate()
    {
        Cache::put(CacheKeys::DEFAULT_CACHE_KEY, 'cached data', 60);

        TestModel::fireModelEvent('created');

        $this->assertNull(Cache::get(CacheKeys::DEFAULT_CACHE_KEY));
    }

    /**
     * @test
     */
    public function modelUpdateAndCache()
    {
        $model = TestModel::factory()->create();

        Cache::put(CacheKeys::DEFAULT_CACHE_KEY, 'cached data', 60);

        $model->update(['name' => 'Updated Name']);

        $this->assertNull(Cache::get(CacheKeys::DEFAULT_CACHE_KEY));
    }
}
