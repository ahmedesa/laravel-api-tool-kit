<?php

namespace Essa\APIToolKit\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait HasCache
{
    protected static function bootHasCache(): void
    {
        static::updated(function (Model $model): void {
            $model->flushCache();
        });

        static::created(function (Model $model): void {
            $model->flushCache();
        });
    }

    public function flushCache(): void
    {
        Cache::forget($this->cacheKey());
    }

    protected function cacheKey(): string
    {
        return 'default.all';
    }
}
