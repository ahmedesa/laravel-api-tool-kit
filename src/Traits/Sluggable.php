<?php

namespace Essa\APIToolKit\Traits;

use Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static findBySlug($slug)
 */
trait Sluggable
{
    protected static function bootSluggable()
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
     * @return mixed
     */
    public function scopeFindBySlug(Builder $query, string $slug)
    {
        return $query->where($this->getSlugFieldName(), $slug)->firstOrFail();
    }

    public function generateSlug()
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
