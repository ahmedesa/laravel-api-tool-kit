<?php

namespace Essa\APIToolKit\Tests\Mocks\Models;

use Essa\APIToolKit\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;

class SluggableTestModel extends Model
{
    use Sluggable;

    protected $guarded = [];
}
