<?php

namespace essa\APIGenerator\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static filter($filter) filter model
 */
trait Filterable
{
    public function scopeFilter($query, QueryFilters $filters): Builder
    {
        return $filters->apply($query);
    }
}
