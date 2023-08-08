<?php

namespace Essa\APIToolKit\Tests\Mocks\Models;

use Essa\APIToolKit\Filters\Filterable;
use Essa\APIToolKit\Tests\Mocks\TestModelFilters;
use Essa\APIToolKit\Traits\HasActivation;
use Essa\APIToolKit\Traits\HasGeneratedCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use HasFactory;
    use Filterable;
    use HasActivation;
    use HasGeneratedCode;

    protected string $default_filters = TestModelFilters::class;

    protected $guarded = [];
}
