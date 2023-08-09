<?php

namespace Essa\APIToolKit\Tests\Mocks\Models;

use Essa\APIToolKit\Filters\Filterable;
use Essa\APIToolKit\Tests\Mocks\TestModelFilters;
use Essa\APIToolKit\Traits\HasActivation;
use Essa\APIToolKit\Traits\HasCache;
use Essa\APIToolKit\Traits\HasGeneratedCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestModel extends Model
{
    use HasFactory;
    use Filterable;
    use HasActivation;
    use HasGeneratedCode;
    use HasCache;

    protected string $default_filters = TestModelFilters::class;

    protected $guarded = [];

    public function sluggableTestModel(): HasMany
    {
        return $this->hasMany(SluggableTestModel::class);
    }
}
