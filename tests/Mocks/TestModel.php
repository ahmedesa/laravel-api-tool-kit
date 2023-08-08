<?php

namespace Essa\APIToolKit\Tests\Mocks;

use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use HasFactory;
    use Filterable;

    protected string $default_filters = TestModelFilters::class;

    protected $guarded = [];
}
