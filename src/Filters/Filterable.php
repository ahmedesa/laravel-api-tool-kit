<?php

namespace Essa\APIToolKit\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static useFilters(string $filterClass = null) filter class
 */
trait Filterable
{
    public function scopeUseFilters(Builder $query, string $filterClass = null): Builder
    {
        if (! property_exists($this, 'default_filters') && is_null($filterClass)) {
            throw new \Exception('please add default_filters property to the model');
        }

        $class = $filterClass ? app($filterClass) : app($this->default_filters);

        return $class->apply($query);
    }
}