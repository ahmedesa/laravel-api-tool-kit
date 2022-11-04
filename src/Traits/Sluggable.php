<?php

namespace Essa\APIToolKit\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * @method static findBySlug($slug)
 */
trait Sluggable
{
    protected static function bootSluggable(): void
    {
        static::creating(function (Model $model) {
            if ($model->enableSluggableInCreating()) {
                $model->generateSlug();
            }
        });

        static::updating(function (Model $model) {
            if ($model->enableSluggableInUpdating()) {
                $model->generateSlug();
            }
        });
    }

    /**
     * find model by slug.
     *
     * @param Builder $query
     * @param string $slug
     *
     * @return Model
     */
    public function scopeFindBySlug(Builder $query, string $slug): Model
    {
        return $query->where($this->getSlugFieldName(), $slug)->firstOrFail();
    }

    public function generateSlug(): void
    {
        $this->{$this->getSlugFieldName()} = Str::slug($this->{$this->getSlugSourceName()});
    }

    protected function enableSluggableInCreating(): bool
    {
        return true;
    }

    protected function enableSluggableInUpdating(): bool
    {
        return true;
    }

    protected function getSlugFieldName(): string
    {
        return 'slug';
    }

    protected function getSlugSourceName(): string
    {
        return 'name';
    }
}
