<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageProjectCategory extends Model
{
    protected $fillable = [
        'project_category_id',
        'order',
        'status',
    ];

    protected $casts = [
        'project_category_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the project category
     */
    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class);
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (!isset($item->order)) {
                $maxOrder = static::max('order');
                $item->order = $maxOrder + 1;
            }
        });
    }
}
