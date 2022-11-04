<?php

namespace Essa\APIToolKit\Traits;

use Carbon\Carbon;

trait DateFilter
{
    public function from_date($term): void
    {
        $this->builder->where($this->getDateColumnName(), '>=', Carbon::parse($term.'00:00:00'));
    }

    public function to_date($term): void
    {
        $this->builder->where($this->getDateColumnName(), '<=', Carbon::parse($term.'23:59:59'));
    }

    public function getDateColumnName(): string
    {
        return 'created_at';
    }
}
