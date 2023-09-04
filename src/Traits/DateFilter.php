<?php

namespace Essa\APIToolKit\Traits;

use Carbon\Carbon;

trait DateFilter
{
    /**
     * Filter records with a date greater than or equal to the specified from date.
     *
     * @param string $term The from date in the format YYYY-MM-DD.
     * @return void
     */
    public function from_date(string $term): void
    {
        $this->builder->where($this->getDateColumnName(), '>=', Carbon::parse($term . '00:00:00'));
    }

    /**
     * Filter records with a date less than or equal to the specified to date.
     *
     * @param string $term The to date in the format YYYY-MM-DD.
     * @return void
     */
    public function to_date(string $term): void
    {
        $this->builder->where($this->getDateColumnName(), '<=', Carbon::parse($term . '23:59:59'));
    }

    /**
     * Get the column name that stores the date.
     *
     * @return string
     */
    public function getDateColumnName(): string
    {
        return 'created_at';
    }
}
