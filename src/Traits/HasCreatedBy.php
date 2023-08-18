<?php

namespace Essa\APIToolKit\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCreatedBy
{
    protected static function bootHasCreatedBy(): void
    {
        static::creating(function (Model $model): void {
            if (auth()->check()) {
                $model->created_by = auth()
                    ->id();
            }
        });
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
