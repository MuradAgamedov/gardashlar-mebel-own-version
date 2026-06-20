<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageProjectCategory extends Model
{
    use App\Traits\HasStatusAndOrder;
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

    /**
     * Scope for ordered items
     */

    /**
     * Auto-increment order when creating
     */
    
}
