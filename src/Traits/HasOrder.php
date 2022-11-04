<?php

namespace Essa\APIToolKit\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasOrder
{
    public static function sort(array $sort): void
    {
        foreach ($sort as $order => $modelId) {
            self::find($modelId)->update(['order' => $order]);
        }
    }

    protected static function bootHasOrder(): void
    {
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order', 'asc');
        });
    }
}