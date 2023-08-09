<?php

namespace Essa\APIToolKit\Tests\Mocks\Models;

use Essa\APIToolKit\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SluggableTestModel extends Model
{
    use Sluggable;

    protected $guarded = [];

    public function testModel(): BelongsTo
    {
        return $this->belongsTo(TestModel::class);
    }
}
