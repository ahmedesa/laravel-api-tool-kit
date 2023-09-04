<?php

namespace Essa\APIToolKit\Traits;

use Carbon\Carbon;

trait TimeFilter
{
    /**
     * Filter records with a timestamp greater than or equal to the specified from time.
     *
     * @param string $term The form time in the format HH:MM:SS.
     * @return void
     */
    public function from_time(string $term): void
    {
        $this->builder->whereTime($this->getTimeColumnName(), '>=', Carbon::parse($term));
    }

    /**
     * Filter records with a timestamp less than or equal to the specified to time.
     *
     * @param string $term The to time in the format HH:MM:SS.
     * @return void
     */
    public function to_time(string $term): void
    {
        $this->builder->whereTime($this->getTimeColumnName(), '<=', Carbon::parse($term));
    }

    /**
     * Get the column name that stores the timestamp.
     *
     * @return string
     */
    public function getTimeColumnName(): string
    {
        return 'created_at';
    }
}
