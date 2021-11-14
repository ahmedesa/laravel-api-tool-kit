<?php

namespace Essa\APIToolKit\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static useFilters($filter_class = null) filter model
 */
trait Filterable
{
    public function scopeUseFilters($query, string $filter_class = null): Builder
    {
        if (! property_exists($this, 'default_filters') && is_null($filter_class)) {
            throw new \Exception('please add default_filters property to the model');
        }

        $class = $filter_class ? app($filter_class) : app($this->default_filters);

        return $class->apply($query);
    }
}