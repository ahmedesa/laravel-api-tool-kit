<?php

namespace essa\APIToolKit\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static active()
 */
trait HasActivation
{
    /**
     * fetch all records that is active.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', 1);
    }

    /**
     *toggle model status (active - deactivate ).
     */
    public function toggleActivation(): void
    {
        $this->is_active = ! $this->is_active;

        $this->save();
    }
}

