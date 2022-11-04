<?php

namespace Essa\APIToolKit\Traits;

use Carbon\Carbon;

trait TimeFilter
{
    public function from_time($term): void
    {
        $this->builder->whereTime($this->getTimeColumnName(), '>=', Carbon::parse($term));
    }

    public function to_time($term): void
    {
        $this->builder->whereTime($this->getTimeColumnName(), '<=', Carbon::parse($term));
    }

    public function getTimeColumnName(): string
    {
        return 'created_at';
    }
}
