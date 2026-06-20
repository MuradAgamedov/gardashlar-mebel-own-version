<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductCategory extends Model
{
    use HasFactory, App\Traits\Translatable, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'parent_id',
        'order',
        'status',
        'image'
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    protected $appends = ['image_url'];

    /**
     * Get all translations for this product category
     */
    public function translates()
    {
        return $this->hasMany(ProductCategoryTranslate::class);
    }

    /**
     * Get parent category
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Get child categories
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    /**
     * Get all products that have this category
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_category');
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (parent, grandparent, etc.)
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * Check if category has children
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Check if category is a child
     */
    public function isChild()
    {
        return !is_null($this->parent_id);
    }

    /**
     * Get root categories (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active categories
     */

    /**
     * Scope for ordered categories
     */

    /**
     * Get the full URL for the image
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        // Check if it's already a full URL
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }
        
        // Check if it's a storage path
        if (strpos($this->image, 'storage/') === 0) {
            return asset($this->image);
        }
        
        // Default: treat as storage path
        return Storage::exists($this->image) ? Storage::url($this->image) : null;
    }

    /**
     * Auto-increment order when creating
     */
    
} 