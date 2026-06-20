<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait HasStatusAndOrder
{
    /**
     * Boot the trait to automatically assign orders on creation.
     */
    protected static function bootHasStatusAndOrder()
    {
        static::creating(function ($model) {
            if (!isset($model->order) && Schema::hasColumn($model->getTable(), 'order')) {
                $maxOrder = static::max('order');
                $model->order = $maxOrder + 1;
            }
        });
    }

    /**
     * Scope query to active records.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope query to order records by order field.
     */
    public function scopeOrdered($query)
    {
        if (Schema::hasColumn($this->getTable(), 'order')) {
            return $query->orderBy($this->getTable() . '.order');
        }
        return $query;
    }
}
